<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\McqSet;
use App\Models\McqAnswer;
use App\Models\User;
use App\Models\Post;
use App\Helpers\ActionMessageHelper;
use App\Services\SubscriptionAccessManager;

class MCQDashboardController extends Controller
{
    /**
     * Unified MCQ Dashboard - Same for all users
     * Admin sees management options, Users see action options
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        // Get real statistics from database with fallbacks
        $totalQuestions = \App\Models\McqQuestion::count() ?: 0;
        $activeExams = McqSet::where('status', 'approved')->count() ?: 0;
        $totalStudents = \App\Models\User::where('role', '!=', 'admin')->count() ?: 0;
        
        $myExamsTaken = 0;
        $myAvgScore = 0;
        
        try {
            $myExamsTaken = McqAnswer::where('user_id', $user->id)->distinct('mcq_set_id')->count() ?: 0;
            
            if ($myExamsTaken > 0) {
                $userResults = McqAnswer::where('user_id', $user->id)
                    ->selectRaw('mcq_set_id, AVG(CASE WHEN is_correct = 1 THEN 100 ELSE 0 END) as avg_score')
                    ->groupBy('mcq_set_id')
                    ->get();
                $myAvgScore = $userResults->avg('avg_score') ?? 0;
            }
        } catch (\Exception $e) {
            // Handle case where mcq_answers table doesn't exist yet
            $myExamsTaken = 0;
            $myAvgScore = 0;
        }
        
        // Calculate real global average score
        $globalAvgScore = 0;
        try {
            $globalAvgScore = McqAnswer::selectRaw('AVG(CASE WHEN is_correct = 1 THEN 100 ELSE 0 END) as avg_score')
                ->value('avg_score') ?? 0;
        } catch (\Exception $e) {
            $globalAvgScore = 0;
        }
        
        // Get user's rank
        $userRank = $this->getUserRank($user->id);
        
        $stats = [
            'totalQuestions' => $totalQuestions,
            'activeExams' => $activeExams,
            'totalStudents' => $totalStudents,
            'avgScore' => round($globalAvgScore, 1),
            'myExamsTaken' => $myExamsTaken,
            'myAvgScore' => round($myAvgScore, 1),
            'userRank' => $userRank,
        ];
        
        // Get real available exams for students
        $availableExams = McqSet::where('status', 'approved')
            ->with('questions')
            ->withCount('questions')
            ->latest()
            ->limit(3)
            ->get();
            
        // Get real leaderboard data
        $leaderboard = $this->getTopUsers(5);
        
        // Get recent posts
        $recentPosts = Post::with(['user', 'category'])
            ->where('status', 'published')
            ->latest()
            ->limit(3)
            ->get();

        $notesPreview = collect();
        $canViewNotesHub = $user->hasPageAccess('notes');

        if ($canViewNotesHub) {
            $notesPreview = $user->mcqNotes()
                ->with(['question.mcqSet'])
                ->latest('updated_at')
                ->take(3)
                ->get();
        }

        return view('mcq.dashboard', compact(
            'stats',
            'isAdmin',
            'user',
            'availableExams',
            'leaderboard',
            'recentPosts',
            'notesPreview',
            'canViewNotesHub'
        ));
    }

    public function questions()
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        // Get real questions from database
        $questionsQuery = \App\Models\McqQuestion::with('mcqSet')
            ->latest()
            ->paginate(20);
            
        if (!$isAdmin) {
                // Get recent notes
                $notesPreview = $user?->mcqNotes()
                    ->latest()
                    ->take(3)
                    ->get() ?? collect();
            // Non-admin users only see questions from approved sets
            $questionsQuery = \App\Models\McqQuestion::with('mcqSet')
                ->whereHas('mcqSet', function($q) {
                    $q->where('status', 'approved');
                })
                ->latest()
                ->paginate(20);
        }
        
        $stats = [
            'totalQuestions' => \App\Models\McqQuestion::count(),
            'practiceQuestions' => \App\Models\McqQuestion::whereHas('mcqSet', function($q) {
                $q->where('status', 'approved');
            })->count(),
            'myAttempts' => \App\Models\McqAnswer::where('user_id', $user->id)->distinct('question_id')->count(),
            'correctAnswers' => \App\Models\McqAnswer::where('user_id', $user->id)->where('is_correct', true)->count()
        ];

        return view('mcq.questions', compact('questionsQuery', 'isAdmin', 'user', 'stats'));
    }

    public function examinations()
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        // Fetch MCQ sets from database with questions count
        $mcqSets = McqSet::with('questions', 'user')
            ->withCount('questions')
            ->latest()
            ->get();
        
        // Transform the data to match the view format
        $examinations = $mcqSets->map(function($set) {
            $data = [
                'id' => $set->id,
                'title' => $set->title ?? $set->exam_name,
                'description' => 'Exam: ' . $set->exam_name . 
                    ($set->exam_date ? ' | Date: ' . $set->exam_date->format('M d, Y') : '') .
                    ($set->exam_time ? ' | Time: ' . $set->exam_time : ''),
                'status' => ucfirst($set->status),
                'duration' => $set->duration ?? 60,
                'questions' => $set->questions_count,
            ];
            
            // Add status-specific data
            if ($set->status === 'approved' || $set->status === 'active') {
                $data['status'] = 'Active';
                $data['participants'] = 0; // TODO: Get actual participants count
            } elseif ($set->status === 'pending') {
                $data['status'] = 'Scheduled';
                if ($set->exam_date && $set->exam_time) {
                    $data['start_date'] = $set->exam_date->format('M d, Y') . ' ' . $set->exam_time;
                }
            } else {
                $data['status'] = 'Completed';
                $data['participants'] = 0; // TODO: Get actual participants count
            }
            
            return $data;
        });

        return view('mcq.examinations', compact('examinations', 'isAdmin', 'user'));
    }

    public function analytics()
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        // Get user's exam results
        $userResults = McqAnswer::where('user_id', $user->id)
            ->with(['mcqSet', 'question'])
            ->get()
            ->groupBy('mcq_set_id');
            
        $examResults = [];
        foreach ($userResults as $setId => $answers) {
            $mcqSet = $answers->first()->mcqSet;
            if (!$mcqSet) continue;
            
            $totalQuestions = $mcqSet->questions()->count();
            $correctAnswers = $answers->where('is_correct', true)->count();
            $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;
            
            $examResults[] = [
                'id' => $setId,
                'exam' => $mcqSet->title ?? $mcqSet->exam_name,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'percentage' => $percentage,
                'status' => $percentage >= 60 ? 'Passed' : 'Failed',
                'date' => $answers->first()->created_at->format('M d, Y'),
                'time_taken' => $answers->sum('time_taken') . ' min'
            ];
        }
        
        // Sort by date descending
        usort($examResults, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        $stats = [
            'totalExams' => count($examResults),
            'passedExams' => collect($examResults)->where('status', 'Passed')->count(),
            'averageScore' => collect($examResults)->avg('percentage') ?? 0,
            'bestScore' => collect($examResults)->max('percentage') ?? 0
        ];

        return view('mcq.analytics', compact('examResults', 'stats', 'isAdmin', 'user'));
    }

    public function students()
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        // Get real students from database with fallbacks
        $studentsQuery = \App\Models\User::where('role', '!=', 'admin')
            ->latest()
            ->limit(50);
            
        $students = $studentsQuery->get()->map(function($student) {
            $examsTaken = 0;
            $avgScore = 0;
            
            try {
                // Get exam statistics for each student
                $examsTaken = McqAnswer::where('user_id', $student->id)
                    ->distinct('mcq_set_id')
                    ->count();
                    
                // Calculate average score
                if ($examsTaken > 0) {
                    $userResults = McqAnswer::where('user_id', $student->id)
                        ->selectRaw('mcq_set_id, AVG(CASE WHEN is_correct = 1 THEN 100 ELSE 0 END) as avg_score')
                        ->groupBy('mcq_set_id')
                        ->get();
                    $avgScore = $userResults->avg('avg_score') ?? 0;
                }
            } catch (\Exception $e) {
                // Handle case where mcq_answers table doesn't exist
            }
            
            $nameParts = explode(' ', $student->name);
            $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'initials' => $initials,
                'email' => $student->email,
                'exams_taken' => $examsTaken,
                'avg_score' => round($avgScore, 1) . '%',
                'status' => 'Active'
            ];
        });

        return view('mcq.students', compact('students', 'isAdmin', 'user'));
    }

    public function leaderboard()
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        // Get leaderboard data
        $leaderboardData = \App\Models\User::where('role', '!=', 'admin')
            ->with(['mcqAnswers'])
            ->get()
            ->map(function($student) {
                $answers = $student->mcqAnswers;
                if ($answers->isEmpty()) return null;
                
                $examsTaken = $answers->groupBy('mcq_set_id')->count();
                $totalQuestions = $answers->count();
                $correctAnswers = $answers->where('is_correct', true)->count();
                $avgScore = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
                
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'exams_taken' => $examsTaken,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'avg_score' => round($avgScore, 1),
                    'points' => $correctAnswers * 10 // 10 points per correct answer
                ];
            })
            ->filter()
            ->sortByDesc('avg_score')
            ->values();
            
        // Find current user's rank
        $currentUserRank = null;
        foreach ($leaderboardData as $index => $student) {
            if ($student['id'] == $user->id) {
                $currentUserRank = $index + 1;
                break;
            }
        }
        
        $stats = [
            'myRank' => $currentUserRank ?? 'Not ranked',
            'totalParticipants' => $leaderboardData->count(),
            'myScore' => $leaderboardData->where('id', $user->id)->first()['avg_score'] ?? 0,
            'topScore' => $leaderboardData->first()['avg_score'] ?? 0
        ];

        return view('mcq.leaderboard', compact('leaderboardData', 'stats', 'isAdmin', 'user'));
    }

    public function posts()
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        // Redirect to the actual post management system
        return redirect()->route('mcq.posts.index');
    }

    public function settings()
    {
        $user = Auth::user();
        
        // Only admins can access settings
        if (!$this->checkAdminRole($user)) {
            abort(403, 'Unauthorized access');
        }
        
        $settings = [
            'system_name' => 'MCQ Management System',
            'default_exam_duration' => '60',
            'email_notifications' => true,
            'sms_notifications' => false,
        ];

        return view('mcq.settings', compact('settings', 'user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        // Only admins can update settings
        if (!$this->checkAdminRole($user)) {
            abort(403, 'Unauthorized access');
        }
        
        // Handle settings update
        return redirect()->route('mcq.settings')->with('success', 'Settings updated successfully!');
    }

    /**
     * View exam details
     */
    public function viewExam($id)
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        $mcqSet = McqSet::with('questions', 'user')->findOrFail($id);
        
        return view('mcq.view-exam', compact('mcqSet', 'isAdmin', 'user'));
    }

    /**
     * Take exam page - redirect to new Livewire interface
     */
    public function takeExam($id)
    {
        $mcqSet = McqSet::findOrFail($id);
        return redirect()->route('exams.take', $mcqSet);
    }

    /**
     * Submit exam answers
     */
    public function submitExam(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $mcqSet = McqSet::with('questions')->findOrFail($id);
            
            // Check if user has already submitted this exam
            $existingSubmission = McqAnswer::where('user_id', $user->id)
                ->where('mcq_set_id', $id)
                ->exists();
            
            if ($existingSubmission) {
                ActionMessageHelper::flash('exam_already_taken');
                return redirect()->route('mcq.examinations.results', $id);
            }
            
            // Validate that we have questions
            if ($mcqSet->questions->isEmpty()) {
                ActionMessageHelper::flash('exam_not_found');
                return redirect()->route('mcq.examinations');
            }
            
            // Begin transaction
            DB::beginTransaction();
            
            $totalMarks = 0;
            $obtainedMarks = 0;
            $correctAnswers = 0;
            $wrongAnswers = 0;
            $answersProcessed = 0;
            
            // Process each question
            foreach ($mcqSet->questions as $question) {
                $questionKey = 'question_' . $question->id;
                $selectedAnswer = $request->input($questionKey);
                
                // Skip if no answer selected for this question
                if (!$selectedAnswer) {
                    continue;
                }
                
                // Calculate if answer is correct
                $isCorrect = ($selectedAnswer == $question->correct_ans);
                $marksObtained = $isCorrect ? ($question->marks ?? 1) : 0;
                
                // Save answer
                McqAnswer::create([
                    'user_id' => $user->id,
                    'question_id' => $question->id,
                    'mcq_set_id' => $mcqSet->id,
                    'selected_answer' => $selectedAnswer,
                    'selected_ans' => $selectedAnswer, // For backward compatibility
                    'is_correct' => $isCorrect,
                    'marks_obtained' => $marksObtained,
                    'time_taken' => 0, // Can be calculated from timer in future
                ]);
                
                // Update statistics
                $totalMarks += ($question->marks ?? 1);
                $obtainedMarks += $marksObtained;
                $answersProcessed++;
                
                if ($isCorrect) {
                    $correctAnswers++;
                } else {
                    $wrongAnswers++;
                }
            }
            
            // Check if any answers were processed
            if ($answersProcessed === 0) {
                DB::rollBack();
                ActionMessageHelper::flash('form_validation_failed');
                return redirect()->back();
            }
            
            DB::commit();
            
            // Redirect to results page
            $examData = [
                'title' => $mcqSet->title ?? $mcqSet->exam_name,
                'score' => $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 1) : 0
            ];
            ActionMessageHelper::flash('exam_submit_success');
            return redirect()->route('mcq.examinations.results', $id);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Exam submission failed', [
                'user_id' => Auth::id(),
                'exam_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            ActionMessageHelper::flash('exam_submit_failed');
            return redirect()->back();
        }

        /**
         * Show exam results
         */
        $totalQuestions = $mcqSet->questions->count();
        $answeredQuestions = $userAnswers->count();
        $correctAnswers = $userAnswers->where('is_correct', true)->count();
        $wrongAnswers = $userAnswers->where('is_correct', false)->count();
        $unansweredQuestions = $totalQuestions - $answeredQuestions;
        
        // Calculate total marks - handle questions without marks field
        $totalMarks = 0;
        foreach ($mcqSet->questions as $question) {
            $totalMarks += $question->marks ?? 1; // Default to 1 mark if not set
        }
        
        // Calculate obtained marks
        $obtainedMarks = $userAnswers->sum('marks_obtained');
        
        // Calculate percentage based on total possible marks
        $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
        
        // Determine pass/fail (60% is passing)
        $isPassed = $percentage >= 60;
        
        $statistics = [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'unanswered_questions' => $unansweredQuestions,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers,
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'percentage' => $percentage,
            'accuracy' => $answeredQuestions > 0 ? round(($correctAnswers / $answeredQuestions) * 100, 2) : 0,
            'is_passed' => $isPassed,
        ];
        
        return view('mcq.exam-results', compact('mcqSet', 'userAnswers', 'statistics', 'isAdmin', 'user'));
    }
    
    /**
     * Get user's rank based on average score
     */
    private function getUserRank($userId)
    {
        try {
            // Get all users with their average scores
            $userScores = McqAnswer::select('user_id')
                ->selectRaw('AVG(CASE WHEN is_correct = 1 THEN 100 ELSE 0 END) as avg_score')
                ->groupBy('user_id')
                ->orderBy('avg_score', 'desc')
                ->get();
            
            // Find the user's position
            $rank = $userScores->search(function ($item) use ($userId) {
                return $item->user_id == $userId;
            });
            
            return $rank !== false ? $rank + 1 : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get top users for leaderboard
     */
    private function getTopUsers($limit = 5)
    {
        try {
            return User::select('users.id', 'users.name', 'users.email')
                ->selectRaw('AVG(CASE WHEN mcq_answers.is_correct = 1 THEN 100 ELSE 0 END) as avg_score')
                ->selectRaw('COUNT(DISTINCT mcq_answers.mcq_set_id) as exams_taken')
                ->leftJoin('mcq_answers', 'users.id', '=', 'mcq_answers.user_id')
                ->where('users.role', '!=', 'admin')
                ->groupBy('users.id', 'users.name', 'users.email')
                ->having('exams_taken', '>', 0)
                ->orderBy('avg_score', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
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

    /**
     * Read MCQ Content - Browse and study MCQ materials
     */
    public function read()
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        // Get available MCQ sets for reading
        $mcqSets = McqSet::with(['questions' => function($query) {
            $query->limit(5); // Preview only first 5 questions
        }])
        ->withCount('questions')
        ->where('status', 'approved')
        ->orderBy('created_at', 'desc')
        ->paginate(12);
        
        // Get reading statistics
        $stats = [
            'totalSets' => McqSet::where('status', 'approved')->count(),
            'totalQuestions' => \App\Models\McqQuestion::whereHas('mcqSet', function ($query) {
                $query->where('status', 'approved');
            })->count(),
            'categories' => collect(['BCS', 'BANK', 'NTRCA', 'Bangla', 'English']), // Fallback categories
            'recentlyAdded' => McqSet::where('status', 'approved')
                ->where('created_at', '>=', now()->subDays(7))
                ->count()
        ];
        
        // Try to get real categories if column exists
        try {
            $realCategories = McqSet::where('status', 'approved')
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->pluck('category')
                ->take(10);
            
            if ($realCategories->isNotEmpty()) {
                $stats['categories'] = $realCategories;
            }
        } catch (\Exception $e) {
            // Column doesn't exist or other error, use fallback categories
        }
        
        return view('mcq.read', compact('mcqSets', 'stats', 'user', 'isAdmin'));
    }

    /**
     * Read individual MCQ content - Study specific MCQ set
     */
    public function readContent(McqSet $mcqSet)
    {
        $user = Auth::user();
        $isAdmin = $this->checkAdminRole($user);
        
        // Check if MCQ set is approved (unless admin)
        if (!$isAdmin && $mcqSet->status !== 'approved') {
            return redirect()->route('mcq.read')
                ->with('error', 'This MCQ set is not available for reading.');
        }
        
        $accessManager = SubscriptionAccessManager::for($user);
        $totalQuestions = $mcqSet->questions()->count();
        $totalMarks = $mcqSet->questions()->sum('marks');
        $previewQuestions = $accessManager->questionsForSet($mcqSet, 'mcq_preview');
        $previewLimit = $accessManager->questionLimit('mcq_preview');
        $limitedView = $accessManager->isLimited()
            && $previewLimit !== null
            && $previewQuestions->count() < $totalQuestions;

        $mcqSet->setRelation('questions', $previewQuestions);
        $mcqSet->loadMissing('user');

        $visibleMarks = $previewQuestions->sum('marks');
        $visibleQuestionIds = $previewQuestions->pluck('id')->all();

        // Get user's previous answers for this set (if any)
        $userAnswers = [];
        if ($user && !empty($visibleQuestionIds)) {
            $userAnswers = McqAnswer::where('user_id', $user->id)
                ->where('mcq_set_id', $mcqSet->id)
                ->whereIn('question_id', $visibleQuestionIds)
                ->get()
                ->keyBy('question_id');
        }

        // Reading statistics
        $stats = [
            'totalQuestions' => $totalQuestions,
            'visibleQuestions' => $previewQuestions->count(),
            'totalMarks' => $totalMarks,
            'visibleMarks' => $visibleMarks,
            'duration' => $mcqSet->duration ?? 60,
            'difficulty' => 'Intermediate',
            'hasAttempted' => is_array($userAnswers) ? false : $userAnswers->count() > 0,
            'lastAttempted' => is_array($userAnswers) ? null : ($userAnswers->count() > 0 ? $userAnswers->first()->created_at : null),
        ];

        return view('mcq.read-content', compact(
            'mcqSet',
            'stats',
            'user',
            'isAdmin',
            'userAnswers',
            'limitedView',
            'previewLimit'
        ));
    }
}
