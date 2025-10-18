<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\McqSet;
use App\Models\McqAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SubscriptionAccessManager;

class ExamTaking extends Component
{
    public $mcqSet;
    public $questions;
    public $currentQuestion = 1;
    public $answers = [];
    public $timeRemaining;
    public $examStarted = false;
    public $examCompleted = false;
    public $limitedAttempt = false;
    public $attemptLimit;
    public $totalQuestionCount = 0;

    public function mount(McqSet $mcqSet)
    {
        $this->mcqSet = $mcqSet;
        $this->totalQuestionCount = $mcqSet->questions()->count();
        $accessManager = SubscriptionAccessManager::for(Auth::user());
        $this->attemptLimit = $accessManager->questionLimit('exam_attempt');
        
        // Check if user already completed this exam
        $hasCompleted = McqAnswer::where('user_id', Auth::id())
            ->where('mcq_set_id', $mcqSet->id)
            ->exists();

        if ($hasCompleted) {
            return redirect()->route('results.show', $mcqSet);
        }

        $this->questions = $accessManager->questionsForSet($mcqSet, 'exam_attempt', true);
        $this->limitedAttempt = $accessManager->isLimited()
            && $this->attemptLimit !== null
            && $this->questions->count() < $this->totalQuestionCount;
        $this->timeRemaining = $mcqSet->duration ? $mcqSet->duration * 60 : null;
        
        // Initialize answers array
        foreach ($this->questions as $question) {
            $this->answers[$question->id] = null;
        }
    }

    public function startExam()
    {
        $this->examStarted = true;
        session(['exam_start_time' => now(), 'exam_id' => $this->mcqSet->id]);
    }

    public function goToQuestion($questionNumber)
    {
        $this->currentQuestion = $questionNumber;
    }

    public function nextQuestion()
    {
        if ($this->currentQuestion < $this->questions->count()) {
            $this->currentQuestion++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestion > 1) {
            $this->currentQuestion--;
        }
    }

    public function submitExam()
    {
        $answeredCount = count(array_filter($this->answers));
        
        if ($answeredCount === 0) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'Please answer at least one question before submitting.'
            ]);
            return;
        }

        DB::beginTransaction();
        
        try {
            foreach ($this->answers as $questionId => $selectedAnswer) {
                if ($selectedAnswer === null) continue;
                
                $question = $this->questions->firstWhere('id', $questionId);
                $isCorrect = ($selectedAnswer == $question->correct_ans);
                $marksObtained = $isCorrect ? ($question->marks ?? 1) : 0;

                McqAnswer::create([
                    'user_id' => Auth::id(),
                    'question_id' => $questionId,
                    'mcq_set_id' => $this->mcqSet->id,
                    'selected_answer' => $selectedAnswer,
                    'is_correct' => $isCorrect,
                    'marks_obtained' => $marksObtained,
                    'time_taken' => 0,
                ]);
            }

            DB::commit();
            session()->forget(['exam_start_time', 'exam_id']);
            
            return redirect()->route('results.show', $this->mcqSet)
                ->with('success', 'Exam submitted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'Failed to submit exam. Please try again.'
            ]);
        }
    }

    public function getAnsweredCountProperty()
    {
        return count(array_filter($this->answers));
    }

    public function getProgressPercentageProperty()
    {
        return ($this->answeredCount / $this->questions->count()) * 100;
    }

    public function getCurrentQuestionDataProperty()
    {
        return $this->questions[$this->currentQuestion - 1] ?? null;
    }

    public function render()
    {
        return view('livewire.exam-taking')->layout('layouts.mcq');
    }
}