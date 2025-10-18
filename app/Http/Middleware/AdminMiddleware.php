<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user is admin using multiple methods for compatibility
        $isAdmin = false;
        
        try {
            // Try Spatie role first
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                $isAdmin = true;
            }
        } catch (\Exception $e) {
            // Spatie not working, continue
        }
        
        // Fallback to role column
        if (!$isAdmin && $user->role === 'admin') {
            $isAdmin = true;
        }

        if (!$isAdmin) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}