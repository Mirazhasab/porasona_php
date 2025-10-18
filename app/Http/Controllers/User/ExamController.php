<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\McqSet;
use App\Models\McqQuestion;
use App\Models\McqAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Display available exams for taking.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all approved MCQ sets (allow retaking)
        $availableExams = McqSet::approved()
            ->with(['user', 'questions'])
            ->latest()
            ->paginate(10);

        // Get completed exams for reference
        $completedExams = McqSet::approved()
            ->with(['user', 'questions'])
            ->whereHas('answers', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->paginate(5);

        return view('user.exams.index', compact('availableExams', 'completedExams'));
    }

    /**
     * Show exam details before starting.
     */
    public function show(McqSet $mcqSet)
    {
        $user = Auth::user();
        
        // Check if exam is approved
        if (!$mcqSet->isApproved()) {
            abort(404, 'Exam not found or not available.');
        }

        // Check if user has already taken this exam (for info only)
        $hasCompleted = McqAnswer::where('user_id', $user->id)
            ->where('mcq_set_id', $mcqSet->id)
            ->exists();

        $mcqSet->load(['questions', 'user']);
        
        return view('user.exams.show', compact('mcqSet', 'hasCompleted'));
    }

    /**
     * Take exam with Livewire interface.
     */
    public function takeLivewire(McqSet $mcqSet)
    {
        $user = Auth::user();
        
        // Check if exam is approved
        if (!$mcqSet->isApproved()) {
            abort(404, 'Exam not found or not available.');
        }

        return view('livewire.exam-taking-wrapper', compact('mcqSet'));
    }

    /**
     * Start the exam.
     */
    public function start(McqSet $mcqSet)
    {
        $user = Auth::user();
        
        // Check if exam is approved
        if (!$mcqSet->isApproved()) {
            abort(404, 'Exam not found or not available.');
        }

        // Load questions with randomized order
        $questions = $mcqSet->questions()->inRandomOrder()->get();
        
        if ($questions->isEmpty()) {
            return redirect()->route('exams.show', $mcqSet)
                ->with('error', 'This exam has no questions available.');
        }

        // Store exam start time in session
        session([
            'exam_start_time' => now(),
            'exam_id' => $mcqSet->id,
            'question_order' => $questions->pluck('id')->toArray()
        ]);

        return view('user.exams.take', compact('mcqSet', 'questions'));
    }

    /**
     * Submit exam answers.
     */
    public function submit(Request $request, McqSet $mcqSet)
    {
        $user = Auth::user();
        
        // Validate that exam session exists
        if (!session('exam_start_time') || session('exam_id') != $mcqSet->id) {
            return redirect()->route('exams.show', $mcqSet)
                ->with('error', 'Invalid exam session. Please start the exam again.');
        }

        // Clear any existing answers for retaking
        McqAnswer::where('user_id', $user->id)
            ->where('mcq_set_id', $mcqSet->id)
            ->delete();

        $startTime = Carbon::parse(session('exam_start_time'));
        $totalTimeTaken = now()->diffInSeconds($startTime);

        // Validate answers
        $answers = $request->input('answers', []);
        $questionTimes = $request->input('question_times', []);

        DB::beginTransaction();
        
        try {
            $totalMarks = 0;
            $correctAnswers = 0;
            
            foreach ($answers as $questionId => $selectedAnswer) {
                $question = McqQuestion::find($questionId);
                
                if (!$question || $question->mcq_set_id != $mcqSet->id) {
                    continue;
                }

                $isCorrect = ($selectedAnswer == $question->correct_ans);
                $marksObtained = $isCorrect ? ($question->marks ?? 1) : 0;
                $timeTaken = $questionTimes[$questionId] ?? 0;

                McqAnswer::create([
                    'user_id' => $user->id,
                    'question_id' => $questionId,
                    'mcq_set_id' => $mcqSet->id,
                    'selected_answer' => $selectedAnswer,
                    'is_correct' => $isCorrect,
                    'marks_obtained' => $marksObtained,
                    'time_taken' => $timeTaken,
                ]);

                $totalMarks += $marksObtained;
                if ($isCorrect) {
                    $correctAnswers++;
                }
            }

            DB::commit();

            // Clear exam session
            session()->forget(['exam_start_time', 'exam_id', 'question_order']);

            return redirect()->route('results.show', $mcqSet)
                ->with('success', 'Exam submitted successfully! You scored ' . $totalMarks . ' marks.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'An error occurred while submitting your exam. Please try again.');
        }
    }

    /**
     * Get exam time remaining (AJAX).
     */
    public function timeRemaining(McqSet $mcqSet)
    {
        if (!session('exam_start_time') || session('exam_id') != $mcqSet->id) {
            return response()->json(['error' => 'Invalid exam session'], 400);
        }

        $startTime = Carbon::parse(session('exam_start_time'));
        $duration = $mcqSet->duration; // in minutes
        
        if (!$duration) {
            return response()->json(['unlimited' => true]);
        }

        $elapsedSeconds = now()->diffInSeconds($startTime);
        $totalSeconds = $duration * 60;
        $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        return response()->json([
            'remaining_seconds' => $remainingSeconds,
            'expired' => $remainingSeconds <= 0
        ]);
    }

    /**
     * Auto-submit exam when time expires (AJAX).
     */
    public function autoSubmit(Request $request, McqSet $mcqSet)
    {
        $user = Auth::user();
        
        // Validate that exam session exists
        if (!session('exam_start_time') || session('exam_id') != $mcqSet->id) {
            return response()->json(['error' => 'Invalid exam session'], 400);
        }

        // Check if time has actually expired
        $startTime = Carbon::parse(session('exam_start_time'));
        $duration = $mcqSet->duration;
        
        if ($duration) {
            $elapsedMinutes = now()->diffInMinutes($startTime);
            if ($elapsedMinutes < $duration) {
                return response()->json(['error' => 'Exam time has not expired'], 400);
            }
        }

        // Process the submission similar to submit method
        $answers = $request->input('answers', []);
        $questionTimes = $request->input('question_times', []);

        DB::beginTransaction();
        
        try {
            $totalMarks = 0;
            
            foreach ($answers as $questionId => $selectedAnswer) {
                $question = McqQuestion::find($questionId);
                
                if (!$question || $question->mcq_set_id != $mcqSet->id) {
                    continue;
                }

                $isCorrect = ($selectedAnswer == $question->correct_ans);
                $marksObtained = $isCorrect ? ($question->marks ?? 1) : 0;
                $timeTaken = $questionTimes[$questionId] ?? 0;

                McqAnswer::create([
                    'user_id' => $user->id,
                    'question_id' => $questionId,
                    'mcq_set_id' => $mcqSet->id,
                    'selected_answer' => $selectedAnswer,
                    'is_correct' => $isCorrect,
                    'marks_obtained' => $marksObtained,
                    'time_taken' => $timeTaken,
                ]);

                $totalMarks += $marksObtained;
            }

            DB::commit();

            // Clear exam session
            session()->forget(['exam_start_time', 'exam_id', 'question_order']);

            return response()->json([
                'success' => true,
                'message' => 'Exam auto-submitted due to time expiry.',
                'total_marks' => $totalMarks,
                'redirect_url' => route('results.show', $mcqSet)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json(['error' => 'Failed to submit exam'], 500);
        }
    }
}