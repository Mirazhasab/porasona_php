@extends('layouts.mcq')

@section('title', 'Exam Details - ' . $mcqSet->title)
@section('page-title', 'Exam Details')
@section('page-subtitle', $mcqSet->title)

@section('content')
<div class="max-w-4xl mx-auto p-4">
    <!-- Exam Details Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-indigo-600">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <h4 class="font-semibold flex items-center gap-2" style="color: white !important;">
                    <i class="fas fa-clipboard-list"></i>
                    Exam Details
                </h4>
                <a href="{{ route('exams.index') }}" class="px-3 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors flex items-center gap-1" style="color: white !important;">
                    <i class="fas fa-arrow-left w-4 h-4"></i>
                    Back to Exams
                </a>
            </div>
        </div>
        <div class="p-6">
            <!-- Exam Title -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold mb-2" style="color: #3b82f6 !important;">{{ $mcqSet->title }}</h2>
                @if($mcqSet->exam_name)
                    <h5 class="text-gray-500">{{ $mcqSet->exam_name }}</h5>
                @endif
            </div>

            <!-- Exam Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-question-circle w-5 h-5" style="color: #3b82f6 !important;"></i>
                        <span class="font-medium">Total Questions:</span>
                        <span>{{ $totalQuestionCount }}</span>
                        @if($limitedPreview)
                            <span class="text-xs text-gray-500">(Showing {{ $mcqSet->questions->count() }} preview)</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <i class="fas fa-star w-5 h-5" style="color: #f59e0b !important;"></i>
                        <span class="font-medium">Total Marks:</span>
                        <span>{{ $totalMarks }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <i class="fas fa-user w-5 h-5" style="color: #06b6d4 !important;"></i>
                        <span class="font-medium">Created by:</span>
                        <span>{{ $mcqSet->user->name }}</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @if($mcqSet->duration)
                        <div class="flex items-center gap-3">
                            <i class="fas fa-clock w-5 h-5" style="color: #ef4444 !important;"></i>
                            <span class="font-medium">Duration:</span>
                            <span style="color: #ef4444 !important;">{{ $mcqSet->duration }} minutes</span>
                        </div>
                    @else
                        <div class="flex items-center gap-3">
                            <i class="fas fa-infinity w-5 h-5" style="color: #10b981 !important;"></i>
                            <span class="font-medium">Duration:</span>
                            <span style="color: #10b981 !important;">No time limit</span>
                        </div>
                    @endif

                    @if($mcqSet->exam_date)
                        <div class="flex items-center gap-3">
                            <i class="fas fa-calendar w-5 h-5 text-gray-500"></i>
                            <span class="font-medium">Exam Date:</span>
                            <span>{{ $mcqSet->exam_date->format('M d, Y') }}</span>
                        </div>
                    @endif

                    @if($mcqSet->exam_time)
                        <div class="flex items-center gap-3">
                            <i class="fas fa-clock w-5 h-5 text-gray-500"></i>
                            <span class="font-medium">Exam Time:</span>
                            <span>{{ \Carbon\Carbon::parse($mcqSet->exam_time)->format('g:i A') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Retake Notice -->
            @if(isset($hasCompleted) && $hasCompleted)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h6 class="font-semibold flex items-center gap-2 mb-2" style="color: #d97706 !important;">
                        <i class="fas fa-redo"></i>
                        Retaking Exam
                    </h6>
                    <p class="mb-2">You have already completed this exam before. Taking it again will:</p>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li>Replace your previous results</li>
                        <li>Reset your score and ranking</li>
                        <li>Allow you to practice and improve your skills</li>
                    </ul>
                </div>
            @endif

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h6 class="font-semibold flex items-center gap-2 mb-2" style="color: #2563eb !important;">
                    <i class="fas fa-info-circle"></i>
                    Instructions
                </h6>
                @if($limitedPreview)
                    <div class="bg-white border border-blue-100 rounded-lg p-3 mb-3 text-sm text-blue-700">
                        Preview limited to {{ $previewLimit }} question{{ $previewLimit == 1 ? '' : 's' }}. Start the exam to access your allowed questions or upgrade for full access.
                    </div>
                @endif
                <ul class="list-disc list-inside space-y-1 text-sm">
                    <li>Read each question carefully before selecting your answer</li>
                    <li>You can navigate between questions using the navigation panel</li>
                    <li>Make sure to review your answers before submitting</li>
                    @if($mcqSet->duration)
                        <li style="color: #ef4444 !important;"><strong>Time limit: {{ $mcqSet->duration }} minutes</strong></li>
                        <li style="color: #ef4444 !important;">The exam will auto-submit when time expires</li>
                    @endif
                    <li>{{ isset($hasCompleted) && $hasCompleted ? 'Retaking will replace your previous results' : 'Once submitted, you cannot change your answers' }}</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('exams.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg font-medium hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <i class="fas fa-arrow-left w-4 h-4"></i>
                    Back to Exams
                </a>
                
                @if($mcqSet->questions->count() > 0)
                    <button type="button" class="px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2" style="background-color: #3b82f6 !important; color: white !important;" onclick="showConfirmModal()" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
                        <i class="fas {{ isset($hasCompleted) && $hasCompleted ? 'fa-redo' : 'fa-play' }} w-4 h-4"></i>
                        {{ isset($hasCompleted) && $hasCompleted ? 'Retake Exam' : 'Start Exam' }}
                    </button>
                    @if(isset($hasCompleted) && $hasCompleted)
                        <a href="{{ route('results.show', $mcqSet) }}" class="px-4 py-2 border border-blue-500 text-blue-600 rounded-lg font-medium hover:bg-blue-50 transition-colors flex items-center gap-2">
                            <i class="fas fa-chart-line w-4 h-4"></i>
                            View Previous Results
                        </a>
                    @endif
                @else
                    <button type="button" class="px-6 py-3 bg-gray-400 text-white rounded-lg font-medium cursor-not-allowed flex items-center gap-2" disabled>
                        <i class="fas fa-exclamation-triangle w-4 h-4"></i>
                        No Questions Available
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-yellow-500 to-orange-600">
            <h5 class="font-semibold flex items-center gap-2" style="color: white !important;">
                <i class="fas {{ isset($hasCompleted) && $hasCompleted ? 'fa-redo' : 'fa-exclamation-triangle' }}"></i>
                {{ isset($hasCompleted) && $hasCompleted ? 'Confirm Exam Retake' : 'Confirm Exam Start' }}
            </h5>
        </div>
        <div class="p-4">
            <p class="font-medium mb-3">{{ isset($hasCompleted) && $hasCompleted ? 'Are you sure you want to retake this exam?' : 'Are you sure you want to start this exam?' }}</p>
            @if(isset($hasCompleted) && $hasCompleted)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                    <small><strong>Warning:</strong> Retaking will replace your previous results and scores.</small>
                </div>
            @endif
            <ul class="text-gray-600 text-sm space-y-1 mb-4">
                <li>{{ isset($hasCompleted) && $hasCompleted ? 'Your previous results will be overwritten' : 'Once started, you cannot restart the exam' }}</li>
                @if($mcqSet->duration)
                    <li>You have <strong>{{ $mcqSet->duration }} minutes</strong> to complete</li>
                @endif
                <li>Make sure you have a stable internet connection</li>
                <li>Ensure you won't be interrupted during the exam</li>
            </ul>
        </div>
        <div class="p-4 border-t border-gray-200 flex gap-3">
            <button type="button" onclick="hideConfirmModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-600 rounded-lg font-medium hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-times w-4 h-4"></i>
                Cancel
            </button>
            <a href="{{ route('exams.start', $mcqSet) }}" class="flex-1 px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2" style="background-color: #3b82f6 !important; color: white !important;" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
                <i class="fas {{ isset($hasCompleted) && $hasCompleted ? 'fa-redo' : 'fa-play' }} w-4 h-4"></i>
                {{ isset($hasCompleted) && $hasCompleted ? 'Yes, Retake' : 'Yes, Start' }}
            </a>
        </div>
    </div>
</div>

<script>
function showConfirmModal() {
    document.getElementById('confirmModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideConfirmModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideConfirmModal();
    }
});
</script>
@endsection