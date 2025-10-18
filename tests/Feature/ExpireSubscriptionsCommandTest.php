<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionExpired;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ExpireSubscriptionsCommandTest extends TestCase
{
    public function test_command_expires_subscriptions_and_notifies_user(): void
    {
        Notification::fake();

        $plan = Subscription::factory()->create();

        $userSubscription = UserSubscription::factory()->create([
            'subscription_id' => $plan->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->subDay(),
        ]);

        $this->artisan('subscriptions:expire')->expectsOutput('Expired 1 subscription(s).')->assertExitCode(0);

        $userSubscription->refresh();
        $this->assertEquals(UserSubscription::STATUS_EXPIRED, $userSubscription->status);

        $this->assertTrue(
            SubscriptionLog::where('user_subscription_id', $userSubscription->id)
                ->where('action', 'subscription_expired')
                ->exists()
        );

        Notification::assertSentTo($userSubscription->user, SubscriptionExpired::class);
    }
}
