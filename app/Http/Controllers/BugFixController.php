<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\McqSet;
use App\Models\McqQuestion;
use App\Models\McqAnswer;
use App\Models\UserAccess;

/**
 * Bug Fix Controller - Handles common application fixes and utilities
 */
class BugFixController extends Controller
{
    /**
     * Check if user is admin using multiple methods for compatibility
     */
    public static function checkAdminRole($user)
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

    /**
     * Fix user access permissions
     */
    public function fixUserAccess()
    {
        try {
            $users = User::whereDoesntHave('access')->get();
            
            foreach ($users as $user) {
                UserAccess::create([
                    'user_id' => $user->id,
                    'practice' => true,
                    'exams' => true,
                    'results' => true,
                    'classmate' => false,
                    'leaderboard' => true,
                    'post' => false,
                    'mcq_management' => false,
                    'read_access' => true,
                ]);
            }
            
            return response()->json(['success' => true, 'fixed' => $users->count()]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Fix missing relationships
     */
    public function fixRelationships()
    {
        try {
            // Check for orphaned records
            $orphanedAnswers = McqAnswer::whereDoesntHave('question')->count();
            $orphanedQuestions = McqQuestion::whereDoesntHave('mcqSet')->count();
            
            // Clean up orphaned records
            McqAnswer::whereDoesntHave('question')->delete();
            McqQuestion::whereDoesntHave('mcqSet')->delete();
            
            return response()->json([
                'success' => true,
                'cleaned_answers' => $orphanedAnswers,
                'cleaned_questions' => $orphanedQuestions
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Database health check
     */
    public function healthCheck()
    {
        try {
            $health = [
                'users' => User::count(),
                'mcq_sets' => McqSet::count(),
                'questions' => McqQuestion::count(),
                'answers' => McqAnswer::count(),
                'user_accesses' => UserAccess::count(),
                'database_connection' => 'OK'
            ];
            
            return response()->json($health);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}