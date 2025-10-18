@extends('layouts.mcq')
@php use Illuminate\Support\Str; @endphp

@section('title', 'Exam Result - ' . $mcqSet->title)
@section('page-title', 'Exam Results')
@section('page-subtitle', $mcqSet->title)

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <!-- Result Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1" style="color: white !important;">{{ $mcqSet->title }}</h1>
                    <p style="color: rgba(255,255,255,0.8) !important;">{{ $mcqSet->exam_name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('results.index') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl font-medium transition-colors flex items-center gap-2" style="color: white !important;">
                        <i class="fas fa-arrow-left w-4 h-4"></i>
                        Back to Results
                    </a>
                    <a href="{{ route('results.review', $mcqSet) }}" class="bg-white px-4 py-2 rounded-xl font-medium hover:bg-green-50 transition-colors flex items-center gap-2" style="color: #059669 !important;">
                        <i class="fas fa-search w-4 h-4"></i>
                        Review Answers
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($limitedReview))
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-2xl px-5 py-4 mb-6" role="status" aria-live="polite">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle mt-1"></i>
                <div>
                    <strong>Preview mode:</strong> Your current plan allows review of {{ $reviewLimit }} question{{ $reviewLimit == 1 ? '' : 's' }} per exam. Showing {{ $visibleQuestionCount }} of {{ $totalQuestionCount }} questions.
                </div>
            </div>
        </div>
    @endif

    <!-- Score Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Score -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="relative w-32 h-32 mx-auto mb-4" role="img" aria-label="Overall score {{ $percentage }} percent">
                    <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120" aria-hidden="true" focusable="false">
                        <circle cx="60" cy="60" r="54" stroke="#e5e7eb" stroke-width="8" fill="transparent"/>
                        <circle cx="60" cy="60" r="54" stroke="#10b981" stroke-width="8" fill="transparent" 
                                stroke-dasharray="{{ 2 * pi() * 54 }}" 
                                stroke-dashoffset="{{ 2 * pi() * 54 * (1 - $percentage / 100) }}"
                                class="transition-all duration-1000 ease-out" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-3xl font-bold">{{ $percentage }}%</div>
                            <div class="text-sm text-gray-500">Score</div>
                        </div>
                    </div>
                </div>
                @php
                    $gradeColor = $percentage >= 80 ? '#059669' : ($percentage >= 60 ? '#d97706' : '#dc2626');
                    $gradeBg = $percentage >= 80 ? '#dcfce7' : ($percentage >= 60 ? '#fef3c7' : '#fee2e2');
                    $grade = $percentage >= 80 ? 'Excellent' : ($percentage >= 60 ? 'Good' : 'Needs Improvement');
                @endphp
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" style="color: {{ $gradeColor }} !important; background-color: {{ $gradeBg }} !important;">
                    {{ $grade }}
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #dbeafe !important;">
                            <i class="fas fa-star w-6 h-6" style="color: #2563eb !important;"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">{{ $totalMarks }}</div>
                            <div class="text-sm text-gray-500">Marks Obtained</div>
                            <div class="text-xs text-gray-400">out of {{ $totalPossibleMarks }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #dcfce7 !important;">
                            <i class="fas fa-check-circle w-6 h-6" style="color: #059669 !important;"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">{{ $correctAnswers }}</div>
                            <div class="text-sm text-gray-500">Correct Answers</div>
                            <div class="text-xs text-gray-400">out of {{ $visibleQuestionCount }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #f3e8ff !important;">
                            <i class="fas fa-clock w-6 h-6" style="color: #7c3aed !important;"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">{{ gmdate('H:i:s', $totalTime) }}</div>
                            <div class="text-sm text-gray-500">Time Taken</div>
                            @if($mcqSet->duration)
                                <div class="text-xs text-gray-400">out of {{ $mcqSet->duration }} minutes</div>
                            @else
                                <div class="text-xs text-gray-400">no time limit</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #fef3c7 !important;">
                            <i class="fas fa-trophy w-6 h-6" style="color: #d97706 !important;"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">#{{ $rank }}</div>
                            <div class="text-sm text-gray-500">Your Rank</div>
                            <div class="text-xs text-gray-400">out of {{ $totalParticipants }} participants</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Answer Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-chart-pie w-5 h-5"></i>
                    Answer Analysis
                </h3>
            </div>
            <div class="p-6">
                <div class="relative w-48 h-48 mx-auto">
                    <canvas id="answerChart" width="192" height="192" aria-hidden="true"></canvas>
                </div>
                <p class="mt-4 text-sm text-gray-600" id="answer-analysis-summary">
                    You answered {{ $correctAnswers }} question{{ $correctAnswers == 1 ? '' : 's' }} correctly and {{ max($answeredQuestions - $correctAnswers, 0) }} question{{ max($answeredQuestions - $correctAnswers, 0) == 1 ? '' : 's' }} incorrectly.
                </p>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-info-circle w-5 h-5"></i>
                    Performance Summary
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="font-medium">Accuracy Rate</span>
                    <span class="font-bold" style="color: #059669 !important;">
                        {{ $visibleQuestionCount > 0 ? round(($correctAnswers / $visibleQuestionCount) * 100, 1) : 0 }}%
                    </span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="font-medium">Avg. Time per Question</span>
                    <span class="font-bold" style="color: #2563eb !important;">
                        {{ $answeredQuestions > 0 ? round($totalTime / $answeredQuestions, 1) : 0 }}s
                    </span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="font-medium">Questions Attempted</span>
                    <span class="font-bold" style="color: #7c3aed !important;">
                        {{ $answeredQuestions }} / {{ $visibleQuestionCount }}
                    </span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="font-medium">Completion Date</span>
                    <span class="font-bold text-gray-600">
                        {{ $userAnswers->first()->created_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Question-wise Performance -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fas fa-list w-5 h-5"></i>
                Question-wise Performance
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" aria-describedby="answer-analysis-summary">
                <caption class="sr-only">Question performance details</caption>
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($userAnswers as $index => $answer)
                    <tr class="hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 whitespace-nowrap text-left font-medium">
                            <div class="text-sm font-medium">Q{{ $index + 1 }}</div>
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($answer->is_correct)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #dcfce7 !important; color: #166534 !important;" aria-label="Answered correctly">
                                    <i class="fas fa-check w-3 h-3 mr-1"></i>
                                    Correct
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #fee2e2 !important; color: #991b1b !important;" aria-label="Answered incorrectly">
                                    <i class="fas fa-times w-3 h-3 mr-1"></i>
                                    Wrong
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <span class="font-bold">{{ $answer->marks_obtained }}</span>
                                <span class="text-gray-500">/ {{ $answer->question->marks ?? 1 }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ gmdate('i:s', $answer->time_taken) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm truncate max-w-xs">
                                {{ Str::limit($answer->question->question, 60) }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap justify-center gap-4">
        <a href="{{ route('results.review', $mcqSet) }}" class="px-6 py-3 rounded-xl font-medium transition-colors flex items-center gap-2" style="background-color: #3b82f6 !important; color: white !important;" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
            <i class="fas fa-search w-4 h-4"></i>
            {{ !empty($limitedReview) ? 'Review Allowed Answers' : 'Review All Answers' }}
        </a>
        <a href="{{ route('results.leaderboard', $mcqSet) }}" class="px-6 py-3 rounded-xl font-medium transition-colors flex items-center gap-2" style="background-color: #f59e0b !important; color: white !important;" onmouseover="this.style.backgroundColor='#d97706'" onmouseout="this.style.backgroundColor='#f59e0b'">
            <i class="fas fa-trophy w-4 h-4"></i>
            View Leaderboard
        </a>
        <a href="{{ route('exams.index') }}" class="px-6 py-3 rounded-xl font-medium transition-colors flex items-center gap-2" style="background-color: #10b981 !important; color: white !important;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
            <i class="fas fa-play w-4 h-4"></i>
            Take Another Exam
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Answer Analysis Chart
    const ctx = document.getElementById('answerChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Correct', 'Wrong'],
            datasets: [{
                data: [{{ $correctAnswers }}, {{ max($answeredQuestions - $correctAnswers, 0) }}],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: { size: 12 },
                        color: '#1e293b'
                    }
                }
            }
        }
    });
});
</script>

@push('styles')
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
@endpush
@endsection