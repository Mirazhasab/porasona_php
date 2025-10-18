<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class CheckSubscriptionMiddlewareTest extends TestCase
{
    public function test_middleware_shares_active_subscription_state(): void
    {
        $user = User::factory()->create();
        $plan = Subscription::factory()->create([
            'price' => 10,
        ]);

        View::share('hasActiveSubscription', false);

        $userSubscription = UserSubscription::factory()->create([
            'user_id' => $user->id,
            'subscription_id' => $plan->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $this->actingAs($user)
            ->get(route('subscriptions.index'))
            ->assertStatus(200)
            ->assertViewHas('currentSubscription', function ($subscription) use ($userSubscription) {
                return $subscription && $subscription->id === $userSubscription->id;
            });

        $this->assertTrue((bool) View::shared('hasActiveSubscription'));
    }

    public function test_middleware_marks_guest_user_without_subscription(): void
    {
        $user = User::factory()->create();

        View::share('hasActiveSubscription', true);

        $this->actingAs($user)
            ->get(route('subscriptions.index'))
            ->assertStatus(200)
            ->assertViewHas('currentSubscription', null);

        $this->assertFalse((bool) View::shared('hasActiveSubscription'));
    }
}
