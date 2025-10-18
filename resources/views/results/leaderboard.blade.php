@extends('layouts.mcq')

@section('title', 'Leaderboard - ' . $mcqSet->title)
@section('page-title', 'Leaderboard')
@section('page-subtitle', $mcqSet->title)

@section('content')
<div class="max-w-7xl mx-auto p-4">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-4 overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-600 p-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <h1 class="text-xl font-bold flex items-center gap-2" style="color: white !important;">
                    <i class="fas fa-trophy"></i>
                    Exam Leaderboard
                </h1>
                <div class="flex gap-2">
                    <a href="{{ route('results.global-leaderboard') }}" class="px-3 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors flex items-center gap-1" style="color: white !important;">
                        <i class="fas fa-globe w-4 h-4"></i>
                        <span class="hidden sm:inline">Global</span>
                    </a>
                    <a href="{{ route('results.show', $mcqSet) }}" class="px-3 py-2 bg-white rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-1" style="color: #f59e0b !important;">
                        <i class="fas fa-arrow-left w-4 h-4"></i>
                        <span class="hidden sm:inline">Back</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 3 Performers -->
    @if($leaderboard->count() >= 3)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <!-- 2nd Place -->
            <div class="lg:order-1">
                @php $second = $leaderboard->get(1); @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 text-center transform hover:scale-105 transition-transform">
                    <div class="mb-3">
                        <i class="fas fa-medal text-4xl mb-2" style="color: #6b7280 !important;"></i>
                        <h4 class="text-lg font-bold" style="color: #6b7280 !important;">#2</h4>
                    </div>
                    <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center text-white font-bold" style="background-color: #6b7280 !important;">
                        {{ substr($second->user->name, 0, 2) }}
                    </div>
                    <h5 class="font-semibold mb-1">{{ $second->user->name }}</h5>
                    <p class="text-gray-500 text-sm mb-2">{{ $second->user->email }}</p>
                    <div class="mb-3">
                        <h3 class="text-2xl font-bold" style="color: #6b7280 !important;">{{ $second->total_marks }}</h3>
                        <small class="text-gray-500">{{ $second->percentage }}%</small>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div>
                            <small class="text-gray-500">Accuracy</small>
                            <div class="font-bold">{{ $second->accuracy }}%</div>
                        </div>
                        <div>
                            <small class="text-gray-500">Time</small>
                            <div class="font-bold">{{ gmdate('H:i:s', $second->total_time) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1st Place -->
            <div class="lg:order-2">
                @php $first = $leaderboard->get(0); @endphp
                <div class="bg-white rounded-2xl shadow-lg border-2 p-6 text-center transform scale-105 hover:scale-110 transition-transform" style="border-color: #fbbf24 !important;">
                    <div class="mb-4">
                        <i class="fas fa-crown text-5xl mb-3" style="color: #fbbf24 !important;"></i>
                        <h3 class="text-xl font-bold" style="color: #fbbf24 !important;">#1</h3>
                    </div>
                    <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center font-bold text-lg" style="background-color: #fbbf24 !important; color: #1f2937 !important;">
                        {{ substr($first->user->name, 0, 2) }}
                    </div>
                    <h4 class="text-lg font-bold mb-1">{{ $first->user->name }}</h4>
                    <p class="text-gray-500 text-sm mb-3">{{ $first->user->email }}</p>
                    <div class="mb-4">
                        <h2 class="text-3xl font-bold" style="color: #fbbf24 !important;">{{ $first->total_marks }}</h2>
                        <small class="text-gray-500">{{ $first->percentage }}%</small>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div>
                            <small class="text-gray-500">Accuracy</small>
                            <div class="font-bold">{{ $first->accuracy }}%</div>
                        </div>
                        <div>
                            <small class="text-gray-500">Time</small>
                            <div class="font-bold">{{ gmdate('H:i:s', $first->total_time) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3rd Place -->
            <div class="lg:order-3">
                @php $third = $leaderboard->get(2); @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 text-center transform hover:scale-105 transition-transform">
                    <div class="mb-3">
                        <i class="fas fa-medal text-4xl mb-2" style="color: #cd7f32 !important;"></i>
                        <h4 class="text-lg font-bold" style="color: #cd7f32 !important;">#3</h4>
                    </div>
                    <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center font-bold" style="background-color: #cd7f32 !important; color: white !important;">
                        {{ substr($third->user->name, 0, 2) }}
                    </div>
                    <h5 class="font-semibold mb-1">{{ $third->user->name }}</h5>
                    <p class="text-gray-500 text-sm mb-2">{{ $third->user->email }}</p>
                    <div class="mb-3">
                        <h3 class="text-2xl font-bold" style="color: #cd7f32 !important;">{{ $third->total_marks }}</h3>
                        <small class="text-gray-500">{{ $third->percentage }}%</small>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div>
                            <small class="text-gray-500">Accuracy</small>
                            <div class="font-bold">{{ $third->accuracy }}%</div>
                        </div>
                        <div>
                            <small class="text-gray-500">Time</small>
                            <div class="font-bold">{{ gmdate('H:i:s', $third->total_time) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Complete Leaderboard -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h5 class="font-semibold flex items-center gap-2">
                    <i class="fas fa-list-ol"></i>
                    Complete Rankings
                </h5>
                <div class="text-gray-500 text-sm">
                    {{ $leaderboard->count() }} participants
                </div>
            </div>
        </div>
        <div class="p-4">
            @if($leaderboard->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Score</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Accuracy</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($leaderboard as $participant)
                                <tr class="hover:bg-gray-50 {{ Auth::id() == $participant->user_id ? 'bg-yellow-50' : '' }}">
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            @if($participant->rank <= 3)
                                                @if($participant->rank == 1)
                                                    <i class="fas fa-crown" style="color: #fbbf24 !important;"></i>
                                                @elseif($participant->rank == 2)
                                                    <i class="fas fa-medal" style="color: #6b7280 !important;"></i>
                                                @else
                                                    <i class="fas fa-medal" style="color: #cd7f32 !important;"></i>
                                                @endif
                                            @endif
                                            <span class="font-bold">#{{ $participant->rank }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-500 text-white flex items-center justify-center text-xs font-bold">
                                                {{ substr($participant->user->name, 0, 2) }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="font-semibold truncate">
                                                    {{ $participant->user->name }}
                                                    @if(Auth::id() == $participant->user_id)
                                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full ml-1">You</span>
                                                    @endif
                                                </div>
                                                <div class="text-gray-500 text-sm truncate">{{ $participant->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap hidden sm:table-cell">
                                        <span class="font-bold">{{ $participant->total_marks }}</span>
                                        <span class="text-gray-500">/ {{ $totalPossibleMarks }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        @php
                                            $badgeColor = $participant->percentage >= 80 ? '#10b981' : 
                                                         ($participant->percentage >= 60 ? '#f59e0b' : '#ef4444');
                                            $badgeBg = $participant->percentage >= 80 ? '#dcfce7' : 
                                                      ($participant->percentage >= 60 ? '#fef3c7' : '#fee2e2');
                                        @endphp
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $badgeBg }} !important; color: {{ $badgeColor }} !important;">{{ $participant->percentage }}%</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap hidden lg:table-cell">
                                        <span class="font-bold" style="color: #3b82f6 !important;">{{ $participant->accuracy }}%</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap hidden lg:table-cell">
                                        <span class="text-gray-500">{{ gmdate('H:i:s', $participant->total_time) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-4xl text-gray-400 mb-3"></i>
                    <h5 class="text-gray-500 font-semibold">No Participants Yet</h5>
                    <p class="text-gray-400">Be the first to take this exam!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics -->
    @if($leaderboard->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-indigo-600">
                    <h6 class="font-semibold flex items-center gap-2" style="color: white !important;">
                        <i class="fas fa-chart-bar"></i>
                        Score Distribution
                    </h6>
                </div>
                <div class="p-4">
                    <canvas id="scoreChart" height="200"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-emerald-600">
                    <h6 class="font-semibold flex items-center gap-2" style="color: white !important;">
                        <i class="fas fa-calculator"></i>
                        Exam Statistics
                    </h6>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">Average Score</div>
                            <div class="text-xl font-bold" style="color: #3b82f6 !important;">
                                {{ round($leaderboard->avg('percentage'), 1) }}%
                            </div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">Highest Score</div>
                            <div class="text-xl font-bold" style="color: #10b981 !important;">
                                {{ $leaderboard->max('percentage') }}%
                            </div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">Average Time</div>
                            <div class="text-xl font-bold" style="color: #8b5cf6 !important;">
                                {{ gmdate('H:i:s', $leaderboard->avg('total_time')) }}
                            </div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">Total Participants</div>
                            <div class="text-xl font-bold" style="color: #f59e0b !important;">
                                {{ $leaderboard->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if($leaderboard->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('scoreChart').getContext('2d');
    const scores = @json($leaderboard->pluck('percentage'));
    const ranges = ['0-20%', '21-40%', '41-60%', '61-80%', '81-100%'];
    const rangeCounts = [0, 0, 0, 0, 0];
    
    scores.forEach(score => {
        if (score <= 20) rangeCounts[0]++;
        else if (score <= 40) rangeCounts[1]++;
        else if (score <= 60) rangeCounts[2]++;
        else if (score <= 80) rangeCounts[3]++;
        else rangeCounts[4]++;
    });
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ranges,
            datasets: [{
                label: 'Participants',
                data: rangeCounts,
                backgroundColor: ['#ef4444', '#f97316', '#f59e0b', '#10b981', '#059669'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, color: '#6b7280' } },
                x: { ticks: { color: '#6b7280' } }
            },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endif


@endsection
