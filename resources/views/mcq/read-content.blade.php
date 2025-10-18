@extends('layouts.mcq')

@section('title', 'Read MCQ Content - ' . $mcqSet->title)
@section('page-title', $mcqSet->title)
@section('page-subtitle', 'Study MCQ Content')

@section('content')
<!-- Container -->
<div class="max-w-5xl mx-auto px-3 sm:px-4 lg:px-6 pb-6">
    <!-- Back Button -->
    <div class="mt-4 mb-4">
        <a href="{{ route('mcq.read') }}" class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm" aria-label="Back to MCQ Library">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" aria-hidden="true"></i>
            <span class="font-medium text-sm">Back to Library</span>
        </a>
    </div>

    <!-- MCQ Set Header -->
    <section class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-4 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-1 sm:p-1">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-2">{{ $mcqSet->title }}</h1>
            <div class="flex flex-wrap items-center gap-2">
                @if($mcqSet->category)
                    <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs sm:text-sm rounded-full border border-white/30">
                        <i data-lucide="tag" class="w-3 h-3 mr-1" aria-hidden="true"></i>
                        {{ ucfirst($mcqSet->category) }}
                    </span>
                @endif
                @if($mcqSet->exam_name)
                    <span class="text-xs sm:text-sm text-white/90">{{ $mcqSet->exam_name }}</span>
                @endif
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 p-1 bg-gray-50">
            <div class="text-center">
                <div class="text-blue-600 mb-1">
                    <i data-lucide="help-circle" class="w-5 h-5 sm:w-6 sm:h-6 mx-auto" aria-hidden="true"></i>
                </div>
                <div class="text-xs text-gray-500">Questions</div>
                <div class="text-sm sm:text-lg font-bold text-gray-900">{{ $stats['visibleQuestions'] ?? $stats['totalQuestions'] }}</div>
                @if(!empty($limitedView))
                    <span class="text-xs text-gray-500 block">Previewing {{ $stats['visibleQuestions'] }} of {{ $stats['totalQuestions'] }}</span>
                @endif
            </div>
            <div class="text-center">
                <div class="text-green-600 mb-1">
                    <i data-lucide="award" class="w-5 h-5 sm:w-6 sm:h-6 mx-auto" aria-hidden="true"></i>
                </div>
                <div class="text-xs text-gray-500">Total Marks</div>
                <div class="text-sm sm:text-lg font-bold text-gray-900">{{ $stats['visibleMarks'] ?? $stats['totalMarks'] }}</div>
                @if(!empty($limitedView))
                    <span class="text-xs text-gray-500 block">Part of {{ $stats['totalMarks'] }} total marks</span>
                @endif
            </div>
            <div class="text-center">
                <div class="text-orange-600 mb-1">
                    <i data-lucide="clock" class="w-5 h-5 sm:w-6 sm:h-6 mx-auto" aria-hidden="true"></i>
                </div>
                <div class="text-xs text-gray-500">Duration</div>
                <div class="text-sm sm:text-lg font-bold text-gray-900">{{ $stats['duration'] }} min</div>
            </div>
            <div class="text-center">
                <div class="text-purple-600 mb-1">
                    <i data-lucide="trending-up" class="w-5 h-5 sm:w-6 sm:h-6 mx-auto" aria-hidden="true"></i>
                </div>
                <div class="text-xs text-gray-500">Difficulty</div>
                <div class="text-sm sm:text-lg font-bold text-gray-900">{{ $stats['difficulty'] }}</div>
            </div>
        </div>

        @if(!empty($limitedView))
            <div class="px-4 pb-4">
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs sm:text-sm text-yellow-800">
                    <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1" aria-hidden="true"></i>
                    Preview limited to {{ $previewLimit }} question{{ $previewLimit == 1 ? '' : 's' }}. Subscribe to unlock all {{ $stats['totalQuestions'] }} questions and full explanations.
                </div>
            </div>
        @endif

        @if($stats['hasAttempted'])
            <div class="px-4 pb-4">
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs sm:text-sm text-blue-800">
                    <i data-lucide="info" class="w-4 h-4 inline mr-1" aria-hidden="true"></i>
                    You previously attempted this on <strong>{{ $stats['lastAttempted']->format('M d, Y') }}</strong>
                </div>
            </div>
        @endif
    </section>

    <!-- Questions Content -->
    <article class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <header class="p-1 sm:p-1 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Questions & Answers</h2>
                <div class="flex flex-wrap items-center gap-2">
                    @if(auth()->user()->access && auth()->user()->access->exams)
                        <a href="{{ route('exams.show', $mcqSet->id) }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-colors shadow-sm text-xs sm:text-sm font-medium">
                            <i data-lucide="play" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" aria-hidden="true"></i>
                            Take Exam
                        </a>
                    @endif
                    <button id="toggleAllBtn" onclick="toggleAllAnswers()" class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-colors shadow-sm text-xs sm:text-sm font-medium" aria-pressed="false">
                        <i data-lucide="eye" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" aria-hidden="true"></i>
                        <span id="toggleText">Show All</span>
                    </button>
                </div>
            </div>
        </header>

        <div class="p-1 sm:p-1">
            @if($mcqSet->questions->count() > 0)
                <div class="space-y-4">
                    @foreach($mcqSet->questions as $index => $question)
                        <section class="question-card bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow" aria-labelledby="question-{{ $question->id }}">
                            <!-- Question Header -->
                            <div class="p-1 sm:p-1 border-b border-gray-100">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-lg flex items-center justify-center font-bold text-sm sm:text-base">{{ $index + 1 }}</div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 id="question-{{ $question->id }}" class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 leading-snug">{{ $question->question }}</h3>
                                        <div class="mt-2 inline-flex items-center bg-amber-100 text-amber-800 px-2 py-1 rounded-md text-xs font-medium">
                                            <i data-lucide="award" class="w-3 h-3 inline mr-1" aria-hidden="true"></i>
                                            {{ $question->marks ?? 1 }} {{ ($question->marks ?? 1) == 1 ? 'Mark' : 'Marks' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Options -->
                            <div class="p-1 sm:p-1">
                                <div class="space-y-2">
                                    @for($i = 1; $i <= 4; $i++)
                                        @php
                                            $optionText = $question->{'ans_' . $i};
                                            $isCorrect = $question->correct_ans == $i;
                                            $userSelected = isset($userAnswers[$question->id]) && $userAnswers[$question->id]->selected_answer == $i;
                                        @endphp
                                        @if($optionText)
                                            <div class="option-item p-3 rounded-lg border-2 transition-all duration-200
                                                {{ $isCorrect ? 'bg-green-50 border-green-300 is-correct' : 'bg-gray-50 border-gray-200 hover:border-gray-300' }}
                                                {{ $userSelected && !$isCorrect ? 'bg-red-50 border-red-300 is-selected' : '' }}">
                                                <div class="flex items-start gap-2 sm:gap-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs sm:text-sm font-bold
                                                            {{ $isCorrect ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700' }}
                                                            {{ $userSelected && !$isCorrect ? 'bg-red-500 text-white' : '' }}">
                                                            {{ chr(64 + $i) }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0 text-xs sm:text-sm text-gray-800 font-medium leading-relaxed pt-1">{{ $optionText }}</div>
                                                    @if($isCorrect)
                                                        <div class="flex-shrink-0">
                                                            <i data-lucide="check-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" aria-hidden="true"></i>
                                                        </div>
                                                    @endif
                                                    @if($userSelected && !$isCorrect)
                                                        <div class="flex-shrink-0">
                                                            <i data-lucide="x-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-red-600" aria-hidden="true"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endfor
                                </div>

                                @php
                                    $hasNotes = !empty(trim($question->notes ?? ''));
                                @endphp

                                <!-- Answer Section (Initially Hidden) -->
                                <div class="answer-section hidden mt-4 pt-4 border-t border-gray-200" aria-hidden="true" data-question-id="{{ $question->id }}">
                                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                        <div class="flex items-start gap-2">
                                            <i data-lucide="lightbulb" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs sm:text-sm font-semibold text-green-900 mb-1">Correct Answer</p>
                                                <p class="text-xs sm:text-sm font-medium text-green-800">{{ chr(64 + $question->correct_ans) }}. {{ $question->{'ans_' . $question->correct_ans} }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="notes-section hidden mt-3" aria-hidden="true" data-question-id="{{ $question->id }}">
                                        @if($hasNotes)
                                            <div class="p-1 bg-blue-50 border border-blue-200 rounded-lg mb-3">
                                                <p class="text-xs sm:text-sm font-semibold text-blue-900 mb-1">Official Explanation</p>
                                                <p class="text-xs sm:text-sm text-blue-800 leading-relaxed">{{ $question->notes }}</p>
                                            </div>
                                        @endif

                                        <div class="p-1 bg-white border border-gray-200 rounded-lg">
                                            @livewire('question-notes', ['questionId' => $question->id], key('question-notes-'.$question->id))
                                        </div>
                                    </div>
                                </div>

                                <!-- Individual Controls -->
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <button type="button" onclick="toggleAnswer({{ $index }})" class="answer-toggle-button inline-flex items-center px-3 sm:px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs sm:text-sm font-medium border border-gray-300" aria-expanded="false">
                                        <i data-lucide="eye" class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1 sm:mr-2" aria-hidden="true"></i>
                                        <span class="toggle-text">Show Answer</span>
                                    </button>
                                    <button type="button" onclick="toggleNotes({{ $index }})" class="notes-button inline-flex items-center px-3 sm:px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors text-xs sm:text-sm font-medium border border-gray-300" aria-expanded="false" data-has-notes="{{ $hasNotes ? 'true' : 'false' }}">
                                        <i data-lucide="file-text" class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1 sm:mr-2" aria-hidden="true"></i>
                                        <span class="notes-toggle-text">Show Notes</span>
                                    </button>
                                    <button type="button" onclick="openAddNotes({{ $index }})" class="add-notes-button inline-flex items-center px-3 sm:px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-xs sm:text-sm font-medium border border-blue-200" aria-expanded="false">
                                        <i data-lucide="plus" class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1 sm:mr-2" aria-hidden="true"></i>
                                        Add Notes
                                    </button>
                                </div>
                            </div>
                        </section>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="help-circle" class="w-10 h-10 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Questions Available</h3>
                    <p class="text-sm text-gray-600">This MCQ set doesn't have any questions yet.</p>
                </div>
            @endif
        </div>
    </article>
</div>
@endsection

@push('scripts')
<script>
let allAnswersVisible = false;

function getAnswerSections() {
    return document.querySelectorAll('.answer-section');
}

function getNotesSections() {
    return document.querySelectorAll('.notes-section');
}

function getAnswerToggleTexts() {
    return document.querySelectorAll('.toggle-text');
}

function getAnswerToggleButtons() {
    return document.querySelectorAll('.answer-toggle-button');
}

function getNotesToggleTexts() {
    return document.querySelectorAll('.notes-toggle-text');
}

function getNotesButtons() {
    return document.querySelectorAll('.notes-button');
}

function getAddNotesButtons() {
    return document.querySelectorAll('.add-notes-button');
}

function getQuestionIdByIndex(index) {
    const section = getAnswerSections()[index];
    return section ? section.getAttribute('data-question-id') : null;
}

function getIndexByQuestionId(questionId) {
    if (questionId === undefined || questionId === null) {
        return -1;
    }

    const idString = String(questionId);
    const answers = getAnswerSections();

    for (let i = 0; i < answers.length; i += 1) {
        if (answers[i].getAttribute('data-question-id') === idString) {
            return i;
        }
    }

    return -1;
}

function dispatchToLivewire(eventName, index) {
    const questionId = getQuestionIdByIndex(index);

    if (!questionId) {
        return;
    }

    const numericId = parseInt(questionId, 10);

    if (Number.isNaN(numericId)) {
        return;
    }

    if (window.Livewire && typeof Livewire.dispatch === 'function') {
        Livewire.dispatch(eventName, { questionId: numericId });
    }
}

function showNoteForm(index) {
    const notesSection = getNotesSections()[index];
    const addNotesButton = getAddNotesButtons()[index];

    if (!notesSection) {
        return;
    }

    const noteFormCard = notesSection.querySelector('.note-form-card');
    if (noteFormCard) {
        noteFormCard.classList.remove('hidden');
        setTimeout(() => {
            noteFormCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const textarea = noteFormCard.querySelector('textarea[name="content"]');
            const fallback = noteFormCard.querySelector('input, textarea');
            const focusTarget = textarea || fallback;
            if (focusTarget && typeof focusTarget.focus === 'function') {
                focusTarget.focus({ preventScroll: true });
            }
        }, 80);
    }

    if (addNotesButton) {
        addNotesButton.setAttribute('aria-expanded', 'true');
    }
}

function hideNoteForm(index) {
    const notesSection = getNotesSections()[index];
    const addNotesButton = getAddNotesButtons()[index];

    if (notesSection) {
        const noteFormCard = notesSection.querySelector('.note-form-card');
        if (noteFormCard) {
            noteFormCard.classList.add('hidden');
        }
    }

    if (addNotesButton) {
        addNotesButton.setAttribute('aria-expanded', 'false');
    }
}

function openNotesSection(index) {
    const answerSections = getAnswerSections();
    const notesSections = getNotesSections();
    const answerToggleTexts = getAnswerToggleTexts();
    const answerToggleButtons = getAnswerToggleButtons();
    const notesToggleTexts = getNotesToggleTexts();
    const notesButtons = getNotesButtons();

    const answerSection = answerSections[index];
    const notesSection = notesSections[index];

    if (!answerSection || !notesSection) {
        return;
    }

    if (answerSection.classList.contains('hidden')) {
        answerSection.classList.remove('hidden');
        answerSection.setAttribute('aria-hidden', 'false');
        if (answerToggleTexts[index]) {
            answerToggleTexts[index].textContent = 'Hide';
        }
        if (answerToggleButtons[index]) {
            answerToggleButtons[index].setAttribute('aria-expanded', 'true');
        }
    }

    notesSection.classList.remove('hidden');
    notesSection.setAttribute('aria-hidden', 'false');

    if (notesToggleTexts[index]) {
        notesToggleTexts[index].textContent = 'Hide Notes';
    }
    if (notesButtons[index]) {
        notesButtons[index].setAttribute('aria-expanded', 'true');
    }
}

function closeNotesSection(index) {
    const notesSections = getNotesSections();
    const notesToggleTexts = getNotesToggleTexts();
    const notesButtons = getNotesButtons();

    const notesSection = notesSections[index];

    if (!notesSection) {
        return;
    }

    notesSection.classList.add('hidden');
    notesSection.setAttribute('aria-hidden', 'true');

    if (notesToggleTexts[index]) {
        notesToggleTexts[index].textContent = 'Show Notes';
    }
    if (notesButtons[index]) {
        notesButtons[index].setAttribute('aria-expanded', 'false');
    }

    hideNoteForm(index);
    dispatchToLivewire('close-note-form', index);
}

function toggleAllAnswers() {
    const answerSections = getAnswerSections();
    const toggleText = document.getElementById('toggleText');
    const toggleButtons = getAnswerToggleTexts();

    allAnswersVisible = !allAnswersVisible;

    answerSections.forEach((section, index) => {
        if (allAnswersVisible) {
            section.classList.remove('hidden');
            section.setAttribute('aria-hidden', 'false');
            if (getAnswerToggleButtons()[index]) {
                getAnswerToggleButtons()[index].setAttribute('aria-expanded', 'true');
            }
        } else {
            section.classList.add('hidden');
            section.setAttribute('aria-hidden', 'true');
            if (getAnswerToggleButtons()[index]) {
                getAnswerToggleButtons()[index].setAttribute('aria-expanded', 'false');
            }
            closeNotesSection(index);
        }
    });

    toggleButtons.forEach(button => {
        button.textContent = allAnswersVisible ? 'Hide' : 'Show Answer';
    });

    if (toggleText) {
        toggleText.textContent = allAnswersVisible ? 'Hide All' : 'Show All';
    }
}

function toggleAnswer(index) {
    const answerSections = getAnswerSections();
    const answerSection = answerSections[index];
    const toggleTexts = getAnswerToggleTexts();
    const answerToggleButtons = getAnswerToggleButtons();

    if (!answerSection || !toggleTexts[index]) {
        return;
    }

    const isHidden = answerSection.classList.contains('hidden');

    if (isHidden) {
        answerSection.classList.remove('hidden');
        answerSection.setAttribute('aria-hidden', 'false');
        toggleTexts[index].textContent = 'Hide';
        if (answerToggleButtons[index]) {
            answerToggleButtons[index].setAttribute('aria-expanded', 'true');
        }
    } else {
        answerSection.classList.add('hidden');
        answerSection.setAttribute('aria-hidden', 'true');
        toggleTexts[index].textContent = 'Show Answer';
        if (answerToggleButtons[index]) {
            answerToggleButtons[index].setAttribute('aria-expanded', 'false');
        }
        closeNotesSection(index);
    }
}

function toggleNotes(index) {
    const notesSections = getNotesSections();
    const notesSection = notesSections[index];

    if (!notesSection) {
        return;
    }

    const isHidden = notesSection.classList.contains('hidden');

    if (isHidden) {
        openNotesSection(index);
    } else {
        closeNotesSection(index);
    }
}

function openAddNotes(index) {
    openNotesSection(index);
    showNoteForm(index);
    dispatchToLivewire('open-note-form', index);
}

window.addEventListener('notes-open-form', function(event) {
    const questionId = event.detail ? event.detail.questionId : undefined;
    const index = getIndexByQuestionId(questionId);

    if (index === -1) {
        return;
    }

    openNotesSection(index);
    showNoteForm(index);
});

window.addEventListener('notes-form-reset', function(event) {
    const questionId = event.detail ? event.detail.questionId : undefined;

    if (questionId !== undefined) {
        const index = getIndexByQuestionId(questionId);
        if (index !== -1) {
            hideNoteForm(index);
        }
        return;
    }

    const notesSections = getNotesSections();
    notesSections.forEach((_, idx) => hideNoteForm(idx));
});

// Initialize Lucide icons when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endpush

@push('styles')
<style>
    /* Smooth transitions */
    .question-card {
        transition: all 0.2s ease;
    }
    
    .option-item {
        transition: all 0.15s ease;
    }
    
    .answer-section {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            max-height: 500px;
            transform: translateY(0);
        }
    }

    /* Print-friendly styles */
    @media print {
        body { 
            max-width: 100% !important;
            margin: 0 !important;
            padding: 8px !important;
        }
        
        .rounded-2xl, .rounded-xl, .rounded-lg { 
            border-radius: 4px !important;
        }
        
        * { 
            box-shadow: none !important;
        }

        /* Show all answers in print */
        .answer-section { 
            display: block !important;
            visibility: visible !important;
        }

        /* Hide interactive controls */
        button, [aria-pressed] {
            display: none !important;
        }

        /* Print-friendly colors */
        .bg-gradient-to-r, .bg-gradient-to-br {
            background: #fff !important;
            color: #000 !important;
        }
        
        .text-white {
            color: #000 !important;
        }

        /* Page breaks */
        .question-card { 
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }
    
    /* Responsive text sizing */
    @media (max-width: 640px) {
        body {
            font-size: 14px;
        }
    }
</style>
@endpush
