<?php

namespace Tests\Feature;

use App\Models\McqQuestion;
use App\Models\McqSet;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserAccess;
use App\Models\UserSubscription;
use Tests\TestCase;

class McqSetAccessTest extends TestCase
{
    public function test_non_subscribed_user_sees_limited_questions(): void
    {
        Setting::setValue(Setting::FREE_MCQ_LIMIT_KEY, 3, 'integer');

        $user = User::factory()->create(['role' => 'student']);
        UserAccess::create([
            'user_id' => $user->id,
            'mcq_management' => true,
        ]);

        $mcqSet = McqSet::factory()->create();
        McqQuestion::factory()->count(5)->for($mcqSet)->create();

        $response = $this->actingAs($user)->get(route('mcq_sets.show', $mcqSet));

        $response
            ->assertStatus(200)
            ->assertViewHas('limitedView', true)
            ->assertViewHas('freeLimit', 3)
            ->assertViewHas('mcqSet', function ($viewSet) {
                return $viewSet->questions->count() === 3;
            });
    }

    public function test_active_subscriber_gets_full_question_list(): void
    {
        Setting::setValue(Setting::FREE_MCQ_LIMIT_KEY, 3, 'integer');

        $user = User::factory()->create(['role' => 'student']);
        UserAccess::create([
            'user_id' => $user->id,
            'mcq_management' => true,
        ]);

        $plan = Subscription::factory()->create();
        UserSubscription::factory()->create([
            'user_id' => $user->id,
            'subscription_id' => $plan->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $mcqSet = McqSet::factory()->create();
        McqQuestion::factory()->count(5)->for($mcqSet)->create();

        $this->actingAs($user)
            ->get(route('mcq_sets.show', $mcqSet))
            ->assertStatus(200)
            ->assertViewHas('limitedView', false)
            ->assertViewHas('mcqSet', function ($viewSet) {
                return $viewSet->questions->count() === 5;
            });
    }
}
