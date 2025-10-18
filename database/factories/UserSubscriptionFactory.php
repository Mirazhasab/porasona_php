<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSubscriptionFactory extends Factory
{
    protected $model = UserSubscription::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subscription_id' => Subscription::factory(),
            'status' => UserSubscription::STATUS_PENDING,
            'payment_method' => 'stripe',
            'starts_at' => null,
            'expires_at' => null,
            'metadata' => [
                'factory' => true,
            ],
        ];
    }
}
