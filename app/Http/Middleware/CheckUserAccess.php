<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();
        
        // Allow access if user is admin
        if ($user && $this->checkAdminRole($user)) {
            return $next($request);
        }
        
        // Check if user has the required permission
        if (!$user || !$user->hasPageAccess($permission)) {
            // Redirect with error message or show 403
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied'], 403);
            }
            
            return redirect()->route('mcq.dashboard')
                ->with('error', "You don't have permission to access this page. Please contact your administrator.");
        }
        
        return $next($request);
    }

    /**
     * Check if user is admin using multiple methods for compatibility
     */
    private function checkAdminRole($user)
    {
        if (!$user) return false;
        
        try {
            // Try Spatie role first
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }
        } catch (\Exception $e) {
            // Spatie not working, continue
        }
        
        // Fallback to role column
        return $user->role === 'admin';
    }
}
