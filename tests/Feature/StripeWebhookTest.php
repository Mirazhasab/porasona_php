<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionActivated;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    public function test_invoice_payment_succeeded_activates_subscription(): void
    {
        Notification::fake();

        $plan = Subscription::factory()->create([
            'price' => 19.99,
        ]);

        $userSubscription = UserSubscription::factory()->create([
            'subscription_id' => $plan->id,
            'status' => UserSubscription::STATUS_PENDING,
            'provider_subscription_id' => 'sub_123',
        ]);

        config(['services.stripe.webhook_secret' => null]);

        $payload = [
            'type' => 'invoice.payment_succeeded',
            'data' => [
                'object' => [
                    'id' => 'in_001',
                    'subscription' => 'sub_123',
                    'amount_paid' => 1999,
                    'currency' => 'usd',
                    'status_transitions' => [
                        'paid_at' => now()->timestamp,
                    ],
                ],
            ],
        ];

        $this->postJson(route('stripe.webhook'), $payload)->assertOk();

        $userSubscription->refresh();
        $this->assertEquals(UserSubscription::STATUS_ACTIVE, $userSubscription->status);
        $this->assertNotNull($userSubscription->transaction_id);
        $this->assertTrue($userSubscription->isActive());

        $payment = Payment::where('user_subscription_id', $userSubscription->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('paid', $payment->status);

        Notification::assertSentTo($userSubscription->user, SubscriptionActivated::class);
    }
}
