<?php

namespace App\Http\Controllers;

use App\Models\McqSet;
use App\Models\McqAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SubscriptionAccessManager;

class ResultController extends Controller
{
    /**
     * Display user's exam results.
     */
    public function index()
    {
        $user = Auth::user();
        $accessManager = SubscriptionAccessManager::for($user);
        
        // Get all exams user has completed with results
        $completedExams = McqSet::approved()
            ->with(['user', 'questions'])
            ->whereHas('answers', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->withCount('questions')
            ->get()
            ->map(function($exam) use ($user, $accessManager) {
                $userAnswers = McqAnswer::where('user_id', $user->id)
                    ->where('mcq_set_id', $exam->id)
                    ->get();
                
                $exam->user_total_marks = $userAnswers->sum('marks_obtained');
                $exam->user_correct_answers = $userAnswers->where('is_correct', true)->count();
                $exam->user_total_time = $userAnswers->sum('time_taken');
                $exam->completion_date = $userAnswers->first()->created_at ?? null;
                
                // Calculate percentage
                $visibleQuestions = $accessManager->clampCollection($exam->questions, 'exam_review');
                $exam->visible_questions_count = $visibleQuestions->count();
                $exam->visible_total_marks = $visibleQuestions->sum('marks') ?: $visibleQuestions->count();
                $totalPossibleMarks = $exam->visible_total_marks;
                $exam->percentage = $totalPossibleMarks > 0 ? 
                    round(($exam->user_total_marks / $totalPossibleMarks) * 100, 2) : 0;
                
                return $exam;
            })
            ->sortByDesc('completion_date');

        return view('results.index', compact('completedExams'));
    }

    /**
     * Show detailed result for a specific exam.
     */
    public function show(McqSet $mcqSet)
    {
        $user = Auth::user();
        $accessManager = SubscriptionAccessManager::for($user);
        
        // Check if exam is approved
        if (!$mcqSet->isApproved()) {
            abort(404, 'Exam not found.');
        }

        // Check if user has taken this exam
        $userAnswers = McqAnswer::where('user_id', $user->id)
            ->where('mcq_set_id', $mcqSet->id)
            ->with('question')
            ->get();

        if ($userAnswers->isEmpty()) {
            return redirect()->route('exams.show', $mcqSet)
                ->with('info', 'You haven\'t taken this exam yet.');
        }

        $totalQuestionCount = $mcqSet->questions()->count();
        $visibleQuestions = $accessManager->questionsForSet($mcqSet, 'exam_review');
        $reviewLimit = $accessManager->questionLimit('exam_review');
        $limitedReview = $accessManager->isLimited()
            && $reviewLimit !== null
            && $visibleQuestions->count() < $totalQuestionCount;

    $visibleQuestionIds = array_map('intval', $visibleQuestions->pluck('id')->all());
    $answeredIds = array_map('intval', $userAnswers->pluck('question_id')->all());

        if ($limitedReview) {
            $allowedIds = array_unique(array_merge($visibleQuestionIds, $answeredIds));
            sort($allowedIds);
            $visibleQuestions = $mcqSet->questions()->whereIn('id', $allowedIds)->orderBy('id')->get();
            $userAnswers = $userAnswers->filter(function ($answer) use ($allowedIds) {
                return in_array($answer->question_id, $allowedIds, true);
            })->values();
        }

        $visibleQuestionCount = $visibleQuestions->count();

        // Calculate results
        $totalMarks = $userAnswers->sum('marks_obtained');
        $correctAnswers = $userAnswers->where('is_correct', true)->count();
        $answeredQuestions = $userAnswers->count();
        $totalTime = $userAnswers->sum('time_taken');
        
        // Calculate total possible marks
        $totalPossibleMarks = $visibleQuestions->sum('marks') ?: $visibleQuestionCount;
        $percentage = $totalPossibleMarks > 0 ? 
            round(($totalMarks / $totalPossibleMarks) * 100, 2) : 0;

        // Get rank among all participants
        $allResults = DB::table('mcq_answers')
            ->select('user_id', DB::raw('SUM(marks_obtained) as total_marks'))
            ->where('mcq_set_id', $mcqSet->id)
            ->groupBy('user_id')
            ->orderByDesc('total_marks')
            ->get();

        $rank = $allResults->search(function($result) use ($user) {
            return $result->user_id == $user->id;
        }) + 1;

        $totalParticipants = $allResults->count();

        // Load exam details
        $mcqSet->load(['questions', 'user']);

        return view('results.show', compact(
            'mcqSet',
            'userAnswers',
            'totalMarks',
            'correctAnswers',
            'answeredQuestions',
            'visibleQuestionCount',
            'totalTime',
            'percentage',
            'rank',
            'totalParticipants',
            'totalPossibleMarks',
            'limitedReview',
            'reviewLimit',
            'totalQuestionCount'
        ));
    }

    /**
     * Show detailed answers for review.
     */
    public function review(McqSet $mcqSet)
    {
        $user = Auth::user();
        $accessManager = SubscriptionAccessManager::for($user);
        
        // Check if exam is approved
        if (!$mcqSet->isApproved()) {
            abort(404, 'Exam not found.');
        }

        // Check if user has taken this exam
        $userAnswers = McqAnswer::where('user_id', $user->id)
            ->where('mcq_set_id', $mcqSet->id)
            ->with('question')
            ->get()
            ->keyBy('question_id');

        if ($userAnswers->isEmpty()) {
            return redirect()->route('exams.show', $mcqSet)
                ->with('info', 'You haven\'t taken this exam yet.');
        }

        $totalQuestionCount = $mcqSet->questions()->count();
        $visibleQuestions = $accessManager->questionsForSet($mcqSet, 'exam_review');
        $reviewLimit = $accessManager->questionLimit('exam_review');
        $limitedReview = $accessManager->isLimited()
            && $reviewLimit !== null
            && $visibleQuestions->count() < $totalQuestionCount;

    $visibleIds = array_map('intval', $visibleQuestions->pluck('id')->all());
    $answeredIds = array_map('intval', $userAnswers->keys()->all());
    $allowedIds = $limitedReview ? array_unique(array_merge($visibleIds, $answeredIds)) : $visibleIds;
        sort($allowedIds);

        // Get all questions with user answers
        $questions = $mcqSet->questions()
            ->whereIn('id', $allowedIds ?: [-1])
            ->orderBy('id')
            ->get()
            ->map(function($question) use ($userAnswers) {
                $question->user_answer = $userAnswers->get($question->id);
            return $question;
        });

        return view('results.review', compact(
            'mcqSet',
            'questions',
            'limitedReview',
            'reviewLimit',
            'totalQuestionCount'
        ));
    }

    /**
     * Show leaderboard for a specific exam.
     */
    public function leaderboard(McqSet $mcqSet)
    {
        // Check if exam is approved
        if (!$mcqSet->isApproved()) {
            abort(404, 'Exam not found.');
        }

        // Get top performers
        $leaderboard = DB::table('mcq_answers')
            ->select([
                'user_id',
                DB::raw('SUM(marks_obtained) as total_marks'),
                DB::raw('COUNT(*) as questions_answered'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('SUM(time_taken) as total_time'),
                DB::raw('MIN(created_at) as completion_time')
            ])
            ->where('mcq_set_id', $mcqSet->id)
            ->groupBy('user_id')
            ->orderByDesc('total_marks')
            ->orderBy('total_time')
            ->limit(50)
            ->get();

        // Load user details
        $userIds = $leaderboard->pluck('user_id');
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        // Calculate total possible marks
        $totalPossibleMarks = $mcqSet->questions->sum('marks') ?: $mcqSet->questions->count();

        // Enhance leaderboard data
        $leaderboard = $leaderboard->map(function($result, $index) use ($users, $totalPossibleMarks) {
            $result->rank = $index + 1;
            $result->user = $users->get($result->user_id);
            $result->percentage = $totalPossibleMarks > 0 ? 
                round(($result->total_marks / $totalPossibleMarks) * 100, 2) : 0;
            $result->accuracy = $result->questions_answered > 0 ? 
                round(($result->correct_answers / $result->questions_answered) * 100, 2) : 0;
            
            return $result;
        });

        return view('results.leaderboard', compact('mcqSet', 'leaderboard', 'totalPossibleMarks'));
    }

    /**
     * Show global leaderboard across all exams.
     */
    public function globalLeaderboard()
    {
        // Get top performers across all approved exams
        $globalLeaderboard = DB::table('mcq_answers')
            ->join('mcq_sets', 'mcq_answers.mcq_set_id', '=', 'mcq_sets.id')
            ->select([
                'mcq_answers.user_id',
                DB::raw('COUNT(DISTINCT mcq_answers.mcq_set_id) as exams_taken'),
                DB::raw('SUM(mcq_answers.marks_obtained) as total_marks'),
                DB::raw('COUNT(*) as total_questions'),
                DB::raw('SUM(CASE WHEN mcq_answers.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('AVG(mcq_answers.marks_obtained) as avg_marks_per_question')
            ])
            ->where('mcq_sets.status', 'approved')
            ->groupBy('mcq_answers.user_id')
            ->having('exams_taken', '>=', 1)
            ->orderByDesc('total_marks')
            ->orderByDesc('avg_marks_per_question')
            ->limit(100)
            ->get();

        // Load user details
        $userIds = $globalLeaderboard->pluck('user_id');
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        // Enhance leaderboard data
        $globalLeaderboard = $globalLeaderboard->map(function($result, $index) use ($users) {
            $result->rank = $index + 1;
            $result->user = $users->get($result->user_id);
            $result->accuracy = $result->total_questions > 0 ? 
                round(($result->correct_answers / $result->total_questions) * 100, 2) : 0;
            $result->avg_marks_per_question = round($result->avg_marks_per_question, 2);
            
            return $result;
        });

        return view('results.global-leaderboard', compact('globalLeaderboard'));
    }

    /**
     * Export exam results as CSV (Admin only).
     */
    public function export(McqSet $mcqSet)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can export results.');
        }

        // Get all results for this exam
        $results = DB::table('mcq_answers')
            ->join('users', 'mcq_answers.user_id', '=', 'users.id')
            ->select([
                'users.name',
                'users.email',
                'mcq_answers.user_id',
                DB::raw('SUM(mcq_answers.marks_obtained) as total_marks'),
                DB::raw('COUNT(*) as questions_answered'),
                DB::raw('SUM(CASE WHEN mcq_answers.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('SUM(mcq_answers.time_taken) as total_time'),
                DB::raw('MIN(mcq_answers.created_at) as completion_time')
            ])
            ->where('mcq_answers.mcq_set_id', $mcqSet->id)
            ->groupBy('mcq_answers.user_id', 'users.name', 'users.email')
            ->orderByDesc('total_marks')
            ->get();

        $filename = 'exam_results_' . $mcqSet->id . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($results, $mcqSet) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Name',
                'Email',
                'Total Marks',
                'Questions Answered',
                'Correct Answers',
                'Accuracy (%)',
                'Total Time (seconds)',
                'Completion Time'
            ]);

            // Add data rows
            foreach ($results as $result) {
                $accuracy = $result->questions_answered > 0 ? 
                    round(($result->correct_answers / $result->questions_answered) * 100, 2) : 0;

                fputcsv($file, [
                    $result->name,
                    $result->email,
                    $result->total_marks,
                    $result->questions_answered,
                    $result->correct_answers,
                    $accuracy,
                    $result->total_time,
                    $result->completion_time
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
