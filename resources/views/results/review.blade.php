@extends('layouts.mcq')

@section('title', 'Review Answers - ' . $mcqSet->title)
@section('page-title', 'Review Answers')
@section('page-subtitle', $mcqSet->title)

@section('content')
<div class="max-w-7xl mx-auto p-4">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-4 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <h1 class="text-xl font-bold" style="color: white !important;">
                    <i class="fas fa-search mr-2"></i>
                    Review Answers
                </h1>
                <div class="flex gap-2">
                    <a href="{{ route('results.show', $mcqSet) }}" class="px-3 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors flex items-center gap-1" style="color: white !important;">
                        <i class="fas fa-chart-line w-4 h-4"></i>
                        <span class="hidden sm:inline">View Results</span>
                    </a>
                    <a href="{{ route('results.index') }}" class="px-3 py-2 bg-white rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-1" style="color: #3b82f6 !important;">
                        <i class="fas fa-arrow-left w-4 h-4"></i>
                        <span class="hidden sm:inline">Back</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($limitedReview))
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-2xl px-5 py-4 mb-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle mt-1"></i>
                <div>
                    <strong>Preview mode:</strong> Showing {{ $questions->count() }} of {{ $totalQuestionCount }} questions from this exam. Upgrade to review every question and explanation.
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Options -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-4 p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter questions by answer status">
                <button type="button" class="px-4 py-2 border border-blue-500 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors" onclick="filterQuestions('all', this)" data-filter="all" data-filter-label="all questions" data-filter-button data-default-class="px-4 py-2 border border-blue-500 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors" data-active-class="bg-blue-500 text-white ring-2 ring-offset-2 ring-blue-500" aria-pressed="true">
                    All ({{ $questions->count() }})
                </button>
                <button type="button" class="px-4 py-2 border border-green-500 text-green-600 rounded-lg text-sm font-medium hover:bg-green-50 transition-colors" onclick="filterQuestions('correct', this)" data-filter="correct" data-filter-label="correct answers" data-filter-button data-default-class="px-4 py-2 border border-green-500 text-green-600 rounded-lg text-sm font-medium hover:bg-green-50 transition-colors" data-active-class="ring-2 ring-offset-2 ring-blue-500" aria-pressed="false">
                    Correct ({{ $questions->where('user_answer.is_correct', true)->count() }})
                </button>
                <button type="button" class="px-4 py-2 border border-red-500 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors" onclick="filterQuestions('wrong', this)" data-filter="wrong" data-filter-label="wrong answers" data-filter-button data-default-class="px-4 py-2 border border-red-500 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors" data-active-class="ring-2 ring-offset-2 ring-blue-500" aria-pressed="false">
                    Wrong ({{ $questions->where('user_answer.is_correct', false)->count() }})
                </button>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="showExplanations" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" aria-describedby="explanationToggleStatus">
                <label for="showExplanations" class="text-sm font-medium">Show Explanations</label>
            </div>
        </div>
        <p id="filterStatus" class="sr-only" aria-live="polite"></p>
        <p id="explanationToggleStatus" class="sr-only" aria-live="polite">Explanations are visible.</p>
    </div>

    <!-- Questions Review -->
    @foreach($questions as $index => $question)
        @php
            $userAnswer = $question->user_answer;
            $isCorrect = $userAnswer ? $userAnswer->is_correct : false;
            $selectedAnswer = $userAnswer ? $userAnswer->selected_answer : null;
        @endphp
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-3 question-review-card overflow-hidden" 
             data-status="{{ $isCorrect ? 'correct' : 'wrong' }}">
            <div class="p-3" style="background-color: {{ $isCorrect ? '#10b981' : '#ef4444' }} !important;">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h6 class="font-semibold flex items-center gap-2" style="color: white !important;">
                        <i class="fas {{ $isCorrect ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                        Q{{ $index + 1 }}
                        @if($question->marks)
                            <span class="px-2 py-1 bg-white/20 rounded text-xs">{{ $question->marks }} marks</span>
                        @endif
                    </h6>
                    <div class="text-xs" style="color: rgba(255,255,255,0.9) !important;">
                        @if($userAnswer)
                            Time: {{ gmdate('i:s', $userAnswer->time_taken) }} | 
                            Marks: {{ $userAnswer->marks_obtained }}/{{ $question->marks ?? 1 }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-4">
                <!-- Question Text -->
                <div class="mb-4">
                    <p class="text-lg leading-relaxed">{{ $question->question }}</p>
                </div>

                <!-- Answer Options -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
                    @php $options = ['A' => $question->ans_1, 'B' => $question->ans_2, 'C' => $question->ans_3, 'D' => $question->ans_4]; @endphp
                    @foreach($options as $letter => $answer)
                        @php 
                            $optionNum = array_search($letter, ['A', 'B', 'C', 'D']) + 1;
                            $isCorrectOption = $question->correct_ans == $optionNum;
                            $isSelectedOption = $selectedAnswer == $optionNum;
                        @endphp
                        <div class="p-3 rounded-lg border-2 transition-colors {{ $isCorrectOption ? 'border-green-500 bg-green-50' : ($isSelectedOption && !$isCorrect ? 'border-red-500 bg-red-50' : 'border-gray-200') }}">
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-sm font-bold {{ $isCorrectOption ? 'bg-green-500 text-white' : ($isSelectedOption && !$isCorrect ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-600') }}">
                                    @if($isCorrectOption)
                                        <i class="fas fa-check text-xs"></i>
                                    @elseif($isSelectedOption && !$isCorrect)
                                        <i class="fas fa-times text-xs"></i>
                                    @else
                                        {{ $letter }}
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <span class="font-medium">{{ $letter }}.</span> {{ $answer }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Answer Summary -->
                <div class="bg-gray-50 p-3 rounded-lg border-l-4 border-blue-500 mb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <span class="font-medium">Your Answer:</span>
                            @if($selectedAnswer)
                                <span class="inline-block px-2 py-1 rounded text-xs font-medium ml-2" style="background-color: {{ $isCorrect ? '#dcfce7' : '#fee2e2' }} !important; color: {{ $isCorrect ? '#166534' : '#991b1b' }} !important;">
                                    Option {{ $selectedAnswer }} ({{ ['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D'][$selectedAnswer] }})
                                </span>
                            @else
                                <span class="inline-block px-2 py-1 bg-gray-200 text-gray-600 rounded text-xs font-medium ml-2">Not Answered</span>
                            @endif
                        </div>
                        <div>
                            <span class="font-medium">Correct Answer:</span>
                            <span class="inline-block px-2 py-1 rounded text-xs font-medium ml-2" style="background-color: #dcfce7 !important; color: #166534 !important;">
                                Option {{ $question->correct_ans }} ({{ ['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D'][$question->correct_ans] }})
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Explanation -->
                @if($question->notes)
                    <div class="bg-blue-50 p-3 rounded-lg border-l-4 border-blue-500 explanation-section">
                        <h6 class="font-semibold mb-2 flex items-center gap-2" style="color: #2563eb !important;">
                            <i class="fas fa-lightbulb"></i>
                            Explanation
                        </h6>
                        <p class="text-gray-700 leading-relaxed">{{ $question->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Summary Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-purple-600">
            <h5 class="font-semibold flex items-center gap-2" style="color: white !important;">
                <i class="fas fa-chart-bar"></i>
                Review Summary
            </h5>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-center">
                <div class="p-3">
                    <h3 class="text-2xl font-bold" style="color: #3b82f6 !important;">{{ $questions->count() }}</h3>
                    <p class="text-gray-500 text-sm">Total Questions</p>
                </div>
                <div class="p-3">
                    <h3 class="text-2xl font-bold" style="color: #10b981 !important;">{{ $questions->where('user_answer.is_correct', true)->count() }}</h3>
                    <p class="text-gray-500 text-sm">Correct Answers</p>
                </div>
                <div class="p-3">
                    <h3 class="text-2xl font-bold" style="color: #ef4444 !important;">{{ $questions->where('user_answer.is_correct', false)->count() }}</h3>
                    <p class="text-gray-500 text-sm">Wrong Answers</p>
                </div>
                <div class="p-3">
                    @php
                        $accuracy = $questions->count() > 0 ? 
                            round(($questions->where('user_answer.is_correct', true)->count() / $questions->count()) * 100, 1) : 0;
                    @endphp
                    <h3 class="text-2xl font-bold" style="color: #8b5cf6 !important;">{{ $accuracy }}%</h3>
                    <p class="text-gray-500 text-sm">Accuracy</p>
                </div>
            </div>
            @if(!empty($limitedReview))
                <p class="text-xs text-gray-500 text-center mt-3">Previewing {{ $questions->count() }} of {{ $totalQuestionCount }} questions.</p>
            @endif
        </div>
    </div>
</div>

<script>
function filterQuestions(type, activeButton) {
    if (!activeButton) {
        activeButton = document.querySelector(`[data-filter="${type}"]`);
    }
    const cards = document.querySelectorAll('.question-review-card');
    const buttons = document.querySelectorAll('[data-filter-button]');
    let visibleCount = 0;
    
    // Update button states for screen readers and styling
    buttons.forEach(btn => {
        const isActive = btn === activeButton;
        const defaultClass = btn.getAttribute('data-default-class');
        const activeClass = btn.getAttribute('data-active-class');

        if (defaultClass) {
            btn.className = defaultClass;
        }

        if (isActive && activeClass) {
            activeClass.split(' ').forEach(cls => {
                if (cls.trim().length > 0) {
                    btn.classList.add(cls);
                }
            });
        }

        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
    
    // Filter cards
    cards.forEach(card => {
        const status = card.getAttribute('data-status');
        
        if (type === 'all') {
            card.style.display = 'block';
            visibleCount += 1;
        } else if (type === 'correct' && status === 'correct') {
            card.style.display = 'block';
            visibleCount += 1;
        } else if (type === 'wrong' && status === 'wrong') {
            card.style.display = 'block';
            visibleCount += 1;
        } else {
            card.style.display = 'none';
        }
    });
    const statusRegion = document.getElementById('filterStatus');
    if (statusRegion && activeButton) {
        const label = activeButton.getAttribute('data-filter-label') || type;
        const pluralised = visibleCount === 1 ? 'question' : 'questions';
        statusRegion.textContent = `${visibleCount} ${pluralised} visible when filtered by ${label}.`;
    }
}

function updateExplanationVisibility(toggle) {
    const explanations = document.querySelectorAll('.explanation-section');
    const statusRegion = document.getElementById('explanationToggleStatus');
    explanations.forEach(explanation => {
        const shouldShow = toggle.checked;
        explanation.style.display = shouldShow ? 'block' : 'none';
        explanation.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
    });
    if (statusRegion) {
        statusRegion.textContent = toggle.checked ? 'Explanations are visible.' : 'Explanations are hidden.';
    }
}

// Initialize state for assistive tech when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const defaultButton = document.querySelector('[data-filter="all"]');
    if (defaultButton) {
        filterQuestions('all', defaultButton);
    }

    const explanationToggle = document.getElementById('showExplanations');
    if (explanationToggle) {
        updateExplanationVisibility(explanationToggle);
        explanationToggle.addEventListener('change', function() {
            updateExplanationVisibility(this);
        });
    }
});
</script>

<style>
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>


@endsection
