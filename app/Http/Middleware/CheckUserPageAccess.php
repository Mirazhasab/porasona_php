<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Allow access if user is admin
        if ($user->role === 'admin') {
            return $next($request);
        }
        
        // Check if user has the required permission
        $userAccess = $user->access;
        
        // If no access record exists, deny access
        if (!$userAccess) {
            return $this->denyAccess($request, $permission);
        }
        
        // Check specific permission
        $hasPermission = $userAccess->{$permission} ?? false;
        
        if (!$hasPermission) {
            return $this->denyAccess($request, $permission);
        }
        
        return $next($request);
    }
    
    /**
     * Handle access denial
     */
    private function denyAccess(Request $request, string $permission): Response
    {
        // If it's an AJAX request, return JSON error
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Access denied',
                'message' => "You don't have permission to access this page.",
                'required_permission' => $permission
            ], 403);
        }
        
        // For regular requests, redirect to dashboard with error message
        return redirect()->route('mcq.dashboard')
            ->with('error', "Access denied. You don't have permission to access " . $this->getPermissionName($permission) . ". Please contact your administrator.");
    }
    
    /**
     * Get human-readable permission name
     */
    private function getPermissionName(string $permission): string
    {
        $names = [
            'practice' => 'Practice Questions',
            'exams' => 'My Exams',
            'results' => 'My Results',
            'classmate' => 'Classmates',
            'leaderboard' => 'Leaderboard',
            'post' => 'Posts',
            'mcq_management' => 'MCQ Management'
        ];
        
        return $names[$permission] ?? ucfirst($permission);
    }
}
