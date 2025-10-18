<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\UserSubscription;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $activeSubscription = $user ? $user->latestActiveSubscription() : null;
        $hasActiveSubscription = (bool) $activeSubscription;
        $pendingSubscription = null;

        if ($user && !$hasActiveSubscription) {
            $pendingSubscription = $user->userSubscriptions()
                ->where('status', UserSubscription::STATUS_PENDING)
                ->latest()
                ->first();
        }

        $featureMatrix = Setting::getSubscriptionFeatureMatrix();
        $subscriptionLimits = Setting::getSubscriptionLimits();

        $request->attributes->set('has_active_subscription', $hasActiveSubscription);
        $request->attributes->set('active_subscription', $activeSubscription);
        $request->attributes->set('pending_subscription', $pendingSubscription);
        $request->attributes->set('subscription_feature_matrix', $featureMatrix);
        $request->attributes->set('subscription_limits', $subscriptionLimits);

        view()->share('hasActiveSubscription', $hasActiveSubscription);
        view()->share('activeSubscription', $activeSubscription);
        view()->share('pendingSubscription', $pendingSubscription);
        view()->share('subscriptionFeatureMatrix', $featureMatrix);
        view()->share('subscriptionLimits', $subscriptionLimits);

        if (!$user) {
            return $next($request);
        }

        $user->syncAccessFromMatrix($featureMatrix, $hasActiveSubscription);

        if ($this->isPrivilegedUser($user) || $hasActiveSubscription) {
            return $next($request);
        }

        if ($this->isSubscriptionFlow($request)) {
            return $next($request);
        }

        if ($this->canAccessRoute($request, $user)) {
            return $next($request);
        }

        $message = $pendingSubscription
            ? 'Your payment is pending review. Access will unlock once it is approved.'
            : 'Upgrade or request access from your administrator to use this feature.';

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'requires_subscription' => true,
            ], Response::HTTP_PAYMENT_REQUIRED);
        }

        return redirect()
            ->route('subscriptions.index')
            ->with('error', $message);
    }

    private function isSubscriptionFlow(Request $request): bool
    {
        $route = $request->route();
        $routeName = $route?->getName();

        if ($routeName && Str::is([
            'subscriptions.*',
            'payments.bkash.*',
        ], $routeName)) {
            return true;
        }

        $uri = $route?->uri();
        if ($uri && Str::is('subscriptions*', $uri)) {
            return true;
        }

        return false;
    }

    private function isPrivilegedUser($user): bool
    {
        if (!$user) {
            return false;
        }

        try {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }
        } catch (\Throwable) {
            // Ignore and fallback to role column
        }

        return $user->role === 'admin';
    }

    private function canAccessRoute(Request $request, $user): bool
    {
        $route = $request->route();
        if (!$route) {
            return true;
        }

        $middlewares = $route->gatherMiddleware();
        $permissions = [];

        foreach ($middlewares as $middleware) {
            if (!is_string($middleware)) {
                continue;
            }

            if (Str::startsWith($middleware, 'user.access:')) {
                $parts = explode(':', $middleware, 2);
                if (isset($parts[1])) {
                    $permissions[] = $parts[1];
                }
            }
        }

        if (empty($permissions)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($user->hasPageAccess($permission)) {
                return true;
            }
        }

        return false;
    }
}
