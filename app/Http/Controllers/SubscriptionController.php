<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartSubscriptionCheckoutRequest;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\UserSubscription;
use App\Services\BkashGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class SubscriptionController extends Controller
{
    public function __construct(private readonly BkashGateway $bkashGateway)
    {
        $this->middleware(['auth', 'subscription.check']);
    }

    public function index(): View
    {
        $user = auth()->user();
        $subscriptions = Subscription::active()->orderBy('price')->get();
        $currentSubscription = $user?->latestActiveSubscription();
        $pendingSubscription = $user?->userSubscriptions()
            ->where('status', UserSubscription::STATUS_PENDING)
            ->latest()
            ->first();

        return view('subscriptions.index', [
            'subscriptions' => $subscriptions,
            'currentSubscription' => $currentSubscription,
            'pendingSubscription' => $pendingSubscription,
            'freeLimit' => Setting::getValue(Setting::FREE_MCQ_LIMIT_KEY, 10),
        ]);
    }

    public function checkout(StartSubscriptionCheckoutRequest $request): RedirectResponse
    {
        $user = $request->user();
        $subscription = Subscription::active()->findOrFail($request->input('subscription_id'));
        $data = $request->validated();

        $paymentType = $data['payment_type'];
        $paymentMethod = $paymentType === StartSubscriptionCheckoutRequest::PAYMENT_TYPE_MANUAL ? 'manual' : 'bkash_online';
        $provider = $paymentType === StartSubscriptionCheckoutRequest::PAYMENT_TYPE_MANUAL ? 'manual' : 'bkash';

        $metadata = [
            'initiated_at' => now()->toIso8601String(),
            'payment_type' => $paymentType,
        ];

        if ($paymentType === StartSubscriptionCheckoutRequest::PAYMENT_TYPE_MANUAL) {
            $metadata['manual_reference'] = $data['manual_transaction_reference'];
            $metadata['manual_notes'] = $data['manual_notes'] ?? null;
        }

        $userSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'status' => UserSubscription::STATUS_PENDING,
            'payment_method' => $paymentMethod,
            'metadata' => $metadata,
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'user_subscription_id' => $userSubscription->id,
            'provider' => $provider,
            'payment_method' => $paymentMethod,
            'amount' => $subscription->price,
            'currency' => $subscription->currency,
            'status' => $paymentType === StartSubscriptionCheckoutRequest::PAYMENT_TYPE_MANUAL ? 'pending_review' : 'pending',
            'meta' => [
                'phase' => 'checkout_initiated',
                'payment_type' => $paymentType,
            ],
        ]);

        if ($paymentType === StartSubscriptionCheckoutRequest::PAYMENT_TYPE_MANUAL) {
            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'manual_reference' => $data['manual_transaction_reference'],
                    'manual_notes' => $data['manual_notes'] ?? null,
                ]),
            ]);

            SubscriptionLog::create([
                'user_id' => $user->id,
                'user_subscription_id' => $userSubscription->id,
                'action' => 'manual_payment_submitted',
                'description' => 'Manual payment submitted for review',
                'meta' => [
                    'payment_id' => $payment->id,
                ],
            ]);

            return redirect()
                ->route('subscriptions.index')
                ->with('success', 'Thanks! Your manual payment request has been submitted. Our team will review and activate access shortly.');
        }

        try {
            $invoiceNumber = 'INV-' . $payment->id . '-' . now()->format('YmdHis');

            $checkout = $this->bkashGateway->createCheckout([
                'amount' => number_format($subscription->price, 2, '.', ''),
                'currency' => strtoupper($subscription->currency),
                'payerReference' => (string) $user->id,
                'merchantInvoiceNumber' => $invoiceNumber,
                'callbackURL' => $this->bkashGateway->callbackUrl($payment->id) ?? route('payments.bkash.callback', ['payment' => $payment->id]),
            ]);

            $userSubscription->update([
                'provider_checkout_session_id' => $checkout['payment_id'],
                'metadata' => array_merge($userSubscription->metadata ?? [], [
                    'bkash_payment_id' => $checkout['payment_id'],
                    'bkash_invoice' => $invoiceNumber,
                ]),
            ]);

            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'bkash_payment_id' => $checkout['payment_id'],
                    'redirect_url' => $checkout['redirect_url'],
                ]),
            ]);

            SubscriptionLog::create([
                'user_id' => $user->id,
                'user_subscription_id' => $userSubscription->id,
                'action' => 'bkash_checkout_created',
                'description' => 'bKash checkout session created',
                'meta' => [
                    'payment_id' => $payment->id,
                    'bkash_payment_id' => $checkout['payment_id'],
                ],
            ]);

            return redirect()->away($checkout['redirect_url']);
        } catch (Throwable $exception) {
            Log::error('bKash checkout session failed', [
                'error' => $exception->getMessage(),
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);

            $userSubscription->delete();
            $payment->delete();

            $message = 'Unable to start bKash checkout. Please try again later.';

            if ($exception instanceof \RuntimeException) {
                $message = 'bKash gateway is not configured yet. Please contact the administrator.';
            }

            return redirect()
                ->route('subscriptions.index')
                ->with('error', $message);
        }
    }
}
