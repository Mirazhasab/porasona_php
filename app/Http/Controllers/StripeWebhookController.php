<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SubscriptionLog;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionActivated;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Stripe\Event;
use Stripe\Webhook;
use Throwable;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = $secret
                ? Webhook::constructEvent($payload, $signature, $secret)
                : Event::constructFrom(json_decode($payload, true, 512, JSON_THROW_ON_ERROR));
        } catch (Throwable $exception) {
            Log::error('Stripe webhook signature verification failed', [
                'message' => $exception->getMessage(),
            ]);

            return response(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;
            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }

        return response(['status' => 'ok']);
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $userSubscriptionId = Arr::get($session->metadata ?? [], 'user_subscription_id');
        if (!$userSubscriptionId) {
            Log::warning('Checkout completed without user_subscription_id metadata', ['session_id' => $session->id]);
            return;
        }

        $userSubscription = UserSubscription::find($userSubscriptionId);
        if (!$userSubscription) {
            Log::warning('User subscription not found for checkout session', ['id' => $userSubscriptionId]);
            return;
        }

        $metadata = $userSubscription->metadata ?? [];
        $metadata['checkout_completed_at'] = now()->toIso8601String();

        $userSubscription->update([
            'provider_subscription_id' => $session->subscription ?? null,
            'metadata' => $metadata,
        ]);

        if ($paymentId = Arr::get($session->metadata ?? [], 'payment_id')) {
            if ($payment = Payment::find($paymentId)) {
                $payment->update([
                    'status' => 'processing',
                    'transaction_id' => $session->payment_intent ?? null,
                    'meta' => array_merge($payment->meta ?? [], [
                        'checkout_session_id' => $session->id,
                    ]),
                ]);
            }
        }

        SubscriptionLog::create([
            'user_id' => $userSubscription->user_id,
            'user_subscription_id' => $userSubscription->id,
            'action' => 'checkout_session_completed',
            'description' => 'Stripe checkout session completed',
            'meta' => [
                'session_id' => $session->id,
            ],
        ]);
    }

    private function handleInvoicePaymentSucceeded(object $invoice): void
    {
        $providerSubscriptionId = $invoice->subscription ?? null;
        if (!$providerSubscriptionId) {
            Log::warning('Invoice payment succeeded without subscription reference', ['invoice_id' => $invoice->id]);
            return;
        }

        $userSubscription = UserSubscription::where('provider_subscription_id', $providerSubscriptionId)->first();
        if (!$userSubscription) {
            Log::warning('User subscription not found for provider subscription id', ['provider_subscription_id' => $providerSubscriptionId]);
            return;
        }

        $subscriptionPlan = $userSubscription->subscription;
        if (!$subscriptionPlan) {
            Log::warning('Base subscription plan missing for user subscription', ['user_subscription_id' => $userSubscription->id]);
            return;
        }

        $startsAt = Carbon::now();
        $expiresAt = $this->calculateExpiry($startsAt->copy(), $subscriptionPlan->interval, (int) $subscriptionPlan->interval_count);

        $transactionId = $invoice->id ?? $invoice->payment_intent ?? null;

        $userSubscription->markActive($startsAt, $expiresAt, $transactionId);

        $payment = Payment::where('user_subscription_id', $userSubscription->id)
            ->latest()
            ->first();

        if (!$payment) {
            $payment = new Payment();
        }

        $payment->fill([
            'user_id' => $userSubscription->user_id,
            'user_subscription_id' => $userSubscription->id,
            'provider' => 'stripe',
            'payment_method' => 'card',
            'amount' => $invoice->amount_paid ? $invoice->amount_paid / 100 : $subscriptionPlan->price,
            'currency' => $invoice->currency ?? $subscriptionPlan->currency,
            'status' => 'paid',
            'transaction_id' => $transactionId,
            'paid_at' => $this->resolvePaidAtTimestamp($invoice),
            'meta' => [
                'invoice_id' => $invoice->id,
                'provider_subscription_id' => $providerSubscriptionId,
            ],
        ]);

        $payment->save();

        SubscriptionLog::create([
            'user_id' => $userSubscription->user_id,
            'user_subscription_id' => $userSubscription->id,
            'action' => 'subscription_activated',
            'description' => 'Stripe invoice payment succeeded',
            'meta' => [
                'transaction_id' => $transactionId,
                'invoice_id' => $invoice->id,
            ],
        ]);

        Notification::send($userSubscription->user, new SubscriptionActivated($userSubscription));
    }

    private function calculateExpiry(Carbon $startsAt, string $interval, int $intervalCount): Carbon
    {
        return match ($interval) {
            'day' => $startsAt->copy()->addDays($intervalCount),
            'week' => $startsAt->copy()->addWeeks($intervalCount),
            'year' => $startsAt->copy()->addYears($intervalCount),
            default => $startsAt->copy()->addMonths($intervalCount), // default to monthly behaviour
        };
    }

    private function resolvePaidAtTimestamp(object $invoice): Carbon
    {
        $paidAt = data_get($invoice, 'status_transitions.paid_at');

        if ($paidAt) {
            return Carbon::createFromTimestamp($paidAt);
        }

        $created = data_get($invoice, 'created');
        if ($created) {
            return Carbon::createFromTimestamp($created);
        }

        return Carbon::now();
    }
}
