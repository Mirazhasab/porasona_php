<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Allow users and moderators, but not admins (they have separate routes)
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}