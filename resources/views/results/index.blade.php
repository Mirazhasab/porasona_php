@extends('layouts.mcq')

@section('title', 'My Exam Results')

@section('content')
<div class="p-4 sm:p-6">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-6 h-6 text-green-500"></i>
                    My Exam Results
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View your performance and track your progress</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('exams.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                    Take More Exams
                </a>
                <a href="{{ route('results.global-leaderboard') }}" class="border border-yellow-500 text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <i data-lucide="trophy" class="w-4 h-4"></i>
                    Global Leaderboard
                </a>
            </div>
        </div>
    </div>

    @if($completedExams->count() > 0)
        <!-- Performance Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-600 text-white rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $completedExams->count() }}</h3>
                        <p class="text-blue-100">Exams Completed</p>
                    </div>
                    <i data-lucide="clipboard-check" class="w-8 h-8 text-blue-200"></i>
                </div>
            </div>
            <div class="bg-green-600 text-white rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">{{ number_format($completedExams->avg('percentage'), 1) }}%</h3>
                        <p class="text-green-100">Average Score</p>
                    </div>
                    <i data-lucide="percent" class="w-8 h-8 text-green-200"></i>
                </div>
            </div>
            <div class="bg-purple-600 text-white rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $completedExams->sum('user_total_marks') }}</h3>
                        <p class="text-purple-100">Total Marks</p>
                    </div>
                    <i data-lucide="star" class="w-8 h-8 text-purple-200"></i>
                </div>
            </div>
            <div class="bg-orange-600 text-white rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">{{ gmdate('H:i:s', $completedExams->sum('user_total_time')) }}</h3>
                        <p class="text-orange-100">Total Time</p>
                    </div>
                    <i data-lucide="clock" class="w-8 h-8 text-orange-200"></i>
                </div>
            </div>
        </div>

        <!-- Exam Results List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="list" class="w-5 h-5"></i>
                    Exam Results
                </h2>
            </div>
            
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Percentage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Questions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Completed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($completedExams as $exam)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $exam->title }}</div>
                                        @if($exam->exam_name)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $exam->exam_name }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $exam->user_total_marks }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">/ {{ $exam->visible_total_marks ?? ($exam->total_marks ?? $exam->questions_count) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $percentage = $exam->percentage;
                                        $badgeClass = $percentage >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($percentage >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200');
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $badgeClass }}">{{ $percentage }}%</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-green-600 dark:text-green-400">{{ $exam->user_correct_answers }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">/ {{ $exam->visible_questions_count ?? $exam->questions_count }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-1">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                        {{ gmdate('H:i:s', $exam->user_total_time) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $exam->completion_date ? $exam->completion_date->format('M d, Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-1">
                                        <a href="{{ route('results.show', $exam) }}" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="View Details">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('results.review', $exam) }}" class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors" title="Review Answers">
                                            <i data-lucide="search" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('results.leaderboard', $exam) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors" title="Leaderboard">
                                            <i data-lucide="trophy" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($completedExams as $exam)
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $exam->title }}</h3>
                                @if($exam->exam_name)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $exam->exam_name }}</p>
                                @endif
                            </div>
                            @php
                                $percentage = $exam->percentage;
                                $badgeClass = $percentage >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($percentage >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200');
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $badgeClass }}">{{ $percentage }}%</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Score:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-1">{{ $exam->user_total_marks }}/{{ $exam->visible_total_marks ?? ($exam->total_marks ?? $exam->questions_count) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Questions:</span>
                                <span class="font-semibold text-green-600 dark:text-green-400 ml-1">{{ $exam->user_correct_answers }}/{{ $exam->visible_questions_count ?? $exam->questions_count }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Time:</span>
                                <span class="text-gray-900 dark:text-white ml-1">{{ gmdate('H:i:s', $exam->user_total_time) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Completed:</span>
                                <span class="text-gray-900 dark:text-white ml-1 text-xs">{{ $exam->completion_date ? $exam->completion_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('results.show', $exam) }}" class="flex-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-3 py-2 rounded-lg text-center text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                View Details
                            </a>
                            <a href="{{ route('results.review', $exam) }}" class="flex-1 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 px-3 py-2 rounded-lg text-center text-sm font-medium hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                                Review
                            </a>
                            <a href="{{ route('results.leaderboard', $exam) }}" class="flex-1 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 px-3 py-2 rounded-lg text-center text-sm font-medium hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                                Leaderboard
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mt-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                    Performance Trend
                </h2>
            </div>
            <div class="p-6">
                <canvas id="performanceChart" height="100"></canvas>
            </div>
        </div>
    @else
        <!-- No Results -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
            <div class="p-12 text-center">
                <i data-lucide="trending-up" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Exam Results Yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">You haven't completed any exams yet. Start taking exams to see your results here.</p>
                <a href="{{ route('exams.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center gap-2">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    Take Your First Exam
                </a>
            </div>
        </div>
    @endif
</div>

@if($completedExams->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    const examData = @json($completedExams->values());
    const labels = examData.map(exam => exam.title.length > 20 ? exam.title.substring(0, 20) + '...' : exam.title);
    const percentages = examData.map(exam => exam.percentage);
    const scores = examData.map(exam => exam.user_total_marks);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Percentage Score',
                data: percentages,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Marks Obtained',
                data: scores,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Exams'
                    },
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Percentage (%)'
                    },
                    min: 0,
                    max: 100,
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Marks'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Your Exam Performance Over Time',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
});
</script>
@endif
@endsection
