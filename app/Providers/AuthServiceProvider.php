<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;
use App\Policies\PostPolicy;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-subscriptions', function (User $user): bool {
            return $user->hasRole('admin') || $user->role === 'admin';
        });

        Gate::define('manage-subscription-settings', function (User $user): bool {
            return $user->hasRole('admin') || $user->role === 'admin';
        });
    }
}