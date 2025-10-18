<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SingleSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = Session::getId();
            
            // Check if this user has other active sessions
            $activeSessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->where('id', '!=', $currentSessionId)
                ->get();
            
            // If there are other active sessions, invalidate them
            if ($activeSessions->count() > 0) {
                // Mark other sessions as inactive
                DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->where('id', '!=', $currentSessionId)
                    ->update(['is_active' => false]);
                
                // Delete the actual session files/data for other sessions
                foreach ($activeSessions as $session) {
                    $this->invalidateSession($session->id);
                }
            }
            
            // Update current session info
            DB::table('sessions')
                ->where('id', $currentSessionId)
                ->update([
                    'user_id' => $user->id,
                    'is_active' => true,
                    'login_at' => now(),
                    'device_fingerprint' => $this->generateDeviceFingerprint($request),
                    'updated_at' => now()
                ]);
        }
        
        return $next($request);
    }
    
    /**
     * Generate a device fingerprint based on user agent and IP
     */
    private function generateDeviceFingerprint(Request $request): string
    {
        $userAgent = $request->header('User-Agent', '');
        $ip = $request->ip();
        
        return hash('sha256', $userAgent . '|' . $ip);
    }
    
    /**
     * Invalidate a specific session
     */
    private function invalidateSession(string $sessionId): void
    {
        try {
            // For file-based sessions, delete the session file
            $sessionPath = storage_path('framework/sessions/' . $sessionId);
            if (file_exists($sessionPath)) {
                unlink($sessionPath);
            }
            
            // For database sessions, the session data is already marked as inactive
            // The Laravel session handler will clean it up automatically
        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::warning('Failed to invalidate session: ' . $e->getMessage());
        }
    }
}
