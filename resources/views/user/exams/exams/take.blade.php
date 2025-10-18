@extends('layouts.mcq')

@section('title', 'Taking Exam - ' . $mcqSet->title)
@section('page-title', 'Exam in Progress')
@section('page-subtitle', $mcqSet->title)

@section('content')
<div class="max-w-7xl mx-auto">
    <form id="examForm" action="{{ route('exams.submit', $mcqSet) }}" method="POST">
        @csrf
        
        <!-- Exam Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold mb-1">{{ $mcqSet->title }}</h1>
                        <p class="text-blue-100">{{ $mcqSet->exam_name }}</p>
                    </div>
                    <div class="flex items-center gap-6">
                        @if($mcqSet->duration)
                        <div class="text-center">
                            <div id="timer" class="text-3xl font-mono font-bold">
                                <span id="timeDisplay">{{ sprintf('%02d:00', $mcqSet->duration) }}</span>
                            </div>
                            <p class="text-sm text-blue-100">Time Remaining</p>
                        </div>
                        @endif
                        <button type="button" onclick="submitExam()" class="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-colors flex items-center gap-2">
                            <i data-lucide="send" class="w-5 h-5"></i>
                            Submit Exam
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        <span id="answeredCount">0</span> / {{ $questions->count() }} answered
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                    <div id="progressBar" class="bg-gradient-to-r from-green-400 to-blue-500 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Question Navigation Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="list" class="w-5 h-5"></i>
                            Questions
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-5 lg:grid-cols-4 gap-2" id="questionNav">
                            @foreach($questions as $index => $question)
                            <button type="button" 
                                    class="question-nav-btn w-10 h-10 rounded-lg border-2 border-gray-300 dark:border-gray-600 text-sm font-medium transition-all duration-200 hover:scale-105"
                                    data-question="{{ $index + 1 }}"
                                    onclick="showQuestion({{ $index + 1 }})">
                                {{ $index + 1 }}
                            </button>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 text-xs">
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 rounded bg-blue-500"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Current</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 rounded bg-green-500"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Answered</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 rounded border-2 border-gray-300"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Area -->
            <div class="lg:col-span-3">
                @foreach($questions as $index => $question)
                <div class="question-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 {{ $index === 0 ? '' : 'hidden' }}" 
                     id="question{{ $index + 1 }}">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Question {{ $index + 1 }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }} of {{ $questions->count() }}</p>
                                </div>
                            </div>
                            @if($question->marks)
                            <div class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $question->marks }} {{ $question->marks == 1 ? 'mark' : 'marks' }}
                            </div>
                            @endif
                        </div>
                        
                        <div class="prose dark:prose-invert max-w-none">
                            <p class="text-lg text-gray-800 dark:text-gray-200 leading-relaxed">{{ $question->question }}</p>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            @php $options = ['A' => $question->ans_1, 'B' => $question->ans_2, 'C' => $question->ans_3, 'D' => $question->ans_4]; @endphp
                            @foreach($options as $letter => $answer)
                            <label class="option-label flex items-start gap-4 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 cursor-pointer hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 group">
                                <input type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       value="{{ $loop->iteration }}" 
                                       class="mt-1 w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                       onchange="updateProgress()">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-sm font-bold text-gray-600 dark:text-gray-400 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $letter }}
                                        </div>
                                        <span class="text-gray-800 dark:text-gray-200 leading-relaxed">{{ $answer }}</span>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/50 rounded-b-2xl">
                        <div class="flex justify-between items-center">
                            <button type="button" 
                                    onclick="showQuestion({{ $index }})"
                                    class="px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors flex items-center gap-2 {{ $index === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $index === 0 ? 'disabled' : '' }}>
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                Previous
                            </button>
                            
                            @if($index === $questions->count() - 1)
                            <button type="button" 
                                    onclick="submitExam()"
                                    class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                Submit Exam
                            </button>
                            @else
                            <button type="button" 
                                    onclick="showQuestion({{ $index + 2 }})"
                                    class="px-6 py-3 bg-blue-500 text-white rounded-xl font-medium hover:bg-blue-600 transition-colors flex items-center gap-2">
                                Next
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </form>
</div>

<script>
let currentQuestion = 1;
let totalQuestions = {{ $questions->count() }};

function showQuestion(questionNum) {
    if (questionNum < 1 || questionNum > totalQuestions) return;
    
    document.querySelectorAll('.question-card').forEach(card => card.classList.add('hidden'));
    document.getElementById('question' + questionNum).classList.remove('hidden');
    
    document.querySelectorAll('.question-nav-btn').forEach(btn => {
        btn.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
        btn.classList.add('border-gray-300', 'dark:border-gray-600', 'text-gray-700', 'dark:text-gray-300');
    });
    
    let activeBtn = document.querySelector('[data-question="' + questionNum + '"]');
    if (activeBtn) {
        activeBtn.classList.remove('border-gray-300', 'dark:border-gray-600', 'text-gray-700', 'dark:text-gray-300');
        activeBtn.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
    }
    
    currentQuestion = questionNum;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateProgress() {
    let answered = document.querySelectorAll('input[type="radio"]:checked').length;
    let percentage = (answered / totalQuestions) * 100;
    
    document.getElementById('answeredCount').textContent = answered;
    document.getElementById('progressBar').style.width = percentage + '%';
    
    document.querySelectorAll('input[type="radio"]:checked').forEach(input => {
        let questionId = input.name.match(/\[(\d+)\]/)[1];
        let questionCards = document.querySelectorAll('.question-card');
        
        questionCards.forEach((card, index) => {
            if (card.querySelector('input[name*="[' + questionId + ']"]')) {
                let navBtn = document.querySelector('[data-question="' + (index + 1) + '"]');
                if (navBtn && !navBtn.classList.contains('bg-blue-500')) {
                    navBtn.classList.remove('border-gray-300', 'dark:border-gray-600', 'text-gray-700', 'dark:text-gray-300');
                    navBtn.classList.add('bg-green-500', 'text-white', 'border-green-500');
                }
            }
        });
    });
}

function submitExam() {
    let answered = document.querySelectorAll('input[type="radio"]:checked').length;
    
    if (answered === 0) {
        alert('Please answer at least one question before submitting.');
        return;
    }
    
    if (confirm(`Submit exam with ${answered} of ${totalQuestions} questions answered?`)) {
        document.querySelectorAll('button').forEach(btn => btn.disabled = true);
        document.getElementById('examForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    showQuestion(1);
    updateProgress();
    lucide.createIcons();
    
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        return e.returnValue = 'Your exam progress will be lost.';
    });
    
    document.getElementById('examForm').addEventListener('submit', function() {
        window.removeEventListener('beforeunload', function() {});
    });
});

@if($mcqSet->duration)
let timeRemaining = {{ $mcqSet->duration * 60 }};

function updateTimer() {
    if (timeRemaining <= 0) {
        alert('Time is up! Submitting exam automatically.');
        submitExam();
        return;
    }
    
    let minutes = Math.floor(timeRemaining / 60);
    let seconds = timeRemaining % 60;
    document.getElementById('timeDisplay').textContent = 
        String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    
    if (timeRemaining <= 300) {
        document.getElementById('timer').classList.add('text-red-500');
    }
    
    timeRemaining--;
}

setInterval(updateTimer, 1000);
@endif
</script>
@endsection