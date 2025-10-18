@extends('layouts.mcq')

@section('title', 'Leaderboard - MCQ Pro')
@section('page-title', 'Leaderboard')
@section('page-subtitle', 'Top performing students')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6">
  <div class="bg-white rounded-2xl p-4 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-blue-800 dark:text-blue-600">My Rank</p>
        <p class="text-xl lg:text-2xl font-bold text-blue-900 dark:text-white mt-1">
          {{ is_numeric($stats['myRank']) ? '#' . $stats['myRank'] : $stats['myRank'] }}
        </p>
      </div>
      <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
        <i data-lucide="user" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
      </div>
    </div>
  </div>
  
  <div class="bg-white  rounded-2xl p-4 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-blue-800 dark:text-blue-600">Participants</p>
        <p class="text-xl lg:text-2xl font-bold text-blue-900 dark:text-white mt-1">{{ $stats['totalParticipants'] }}</p>
      </div>
      <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
        <i data-lucide="users" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
      </div>
    </div>
  </div>
  
  <div class="bg-white  rounded-2xl p-4 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-blue-800 dark:text-blue-600">My Score</p>
        <p class="text-xl lg:text-2xl font-bold text-blue-900 dark:text-white mt-1">{{ round($stats['myScore'], 1) }}%</p>
      </div>
      <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
        <i data-lucide="target" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
      </div>
    </div>
  </div>
  
  <div class="bg-white  rounded-2xl p-4 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-blue-800 dark:text-blue-600">Top Score</p>
        <p class="text-xl lg:text-2xl font-bold text-blue-900 dark:text-white mt-1">{{ round($stats['topScore'], 1) }}%</p>
      </div>
      <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
        <i data-lucide="trophy" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"></i>
      </div>
    </div>
  </div>
</div>

<!-- Leaderboard -->
<div class="bg-white  rounded-2xl border border-gray-200 dark:border-gray-700">
  <div class="p-4 lg:p-6 border-b border-gray-200 dark:border-gray-700">
    <h3 class="text-xl font-bold text-blue-900 dark:text-white">Top Performers</h3>
    <p class="text-sm text-blue-800 dark:text-blue-600 mt-1">Students ranked by average score</p>
  </div>
  
  <div class="p-4 lg:p-6">
    @if($leaderboardData->count() > 0)
      <div class="space-y-3 lg:space-y-4">
        @foreach($leaderboardData->take(10) as $index => $student)
        @php
          $rank = $index + 1;
          $isCurrentUser = $student['id'] == auth()->id();
        @endphp
        <div class="flex items-center justify-between p-4 rounded-xl transition-all duration-200 hover:shadow-md
          @if($rank === 1) bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border border-yellow-200 dark:border-yellow-800
          @elseif($rank === 2) bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 border border-gray-200 dark:border-gray-700
          @elseif($rank === 3) bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border border-orange-200 dark:border-orange-800
          @elseif($isCurrentUser) bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-800
          @else bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700
          @endif">
          
          <!-- Mobile Layout -->
          <div class="lg:hidden w-full">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center
                  @if($rank === 1) bg-yellow-400
                  @elseif($rank === 2) bg-gray-400
                  @elseif($rank === 3) bg-orange-400
                  @else bg-primary-500
                  @endif">
                  @if($rank === 1)
                    <i data-lucide="crown" class="w-5 h-5 text-white"></i>
                  @elseif($rank <= 3)
                    <i data-lucide="medal" class="w-5 h-5 text-white"></i>
                  @else
                    <span class="text-sm font-bold text-white">{{ $rank }}</span>
                  @endif
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="font-semibold text-blue-900 dark:text-white truncate">{{ $student['name'] }}</h4>
                  <p class="text-xs text-blue-700 dark:text-blue-600">{{ $student['exams_taken'] }} exams taken</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-lg font-bold {{ $rank <= 3 ? 'text-primary-600 dark:text-primary-400' : 'text-blue-900 dark:text-white' }}">{{ $student['avg_score'] }}%</p>
                <p class="text-xs text-blue-700 dark:text-blue-600">{{ $student['points'] }} pts</p>
              </div>
            </div>
            @if($isCurrentUser)
              <div class="text-center">
                <span class="inline-flex items-center space-x-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-lg text-xs font-medium">
                  <i data-lucide="user" class="w-3 h-3"></i>
                  <span>You</span>
                </span>
              </div>
            @endif
          </div>
          
          <!-- Desktop Layout -->
          <div class="hidden lg:flex lg:items-center lg:justify-between lg:w-full">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center
                @if($rank === 1) bg-yellow-400
                @elseif($rank === 2) bg-gray-400
                @elseif($rank === 3) bg-orange-400
                @else bg-primary-500
                @endif">
                @if($rank === 1)
                  <i data-lucide="crown" class="w-6 h-6 text-white"></i>
                @elseif($rank <= 3)
                  <i data-lucide="medal" class="w-6 h-6 text-white"></i>
                @else
                  <span class="text-lg font-bold text-white">{{ $rank }}</span>
                @endif
              </div>
              <div>
                <div class="flex items-center space-x-2">
                  <h4 class="font-semibold text-blue-900 dark:text-white">{{ $student['name'] }}</h4>
                  @if($isCurrentUser)
                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-lg text-xs font-medium">You</span>
                  @endif
                </div>
                <p class="text-sm text-blue-800 dark:text-blue-600">{{ $student['exams_taken'] }} exams • {{ $student['correct_answers'] }}/{{ $student['total_questions'] }} correct</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-2xl font-bold {{ $rank <= 3 ? 'text-primary-600 dark:text-primary-400' : 'text-blue-900 dark:text-white' }}">{{ $student['avg_score'] }}%</p>
              <p class="text-sm text-blue-700 dark:text-blue-600">{{ $student['points'] }} points</p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-12">
        <i data-lucide="trophy" class="w-12 h-12 text-blue-600 dark:text-blue-800 mx-auto mb-4"></i>
        <h3 class="text-lg font-medium text-blue-900 dark:text-white mb-2">No Rankings Yet</h3>
        <p class="text-blue-800 dark:text-blue-600 mb-4">Take some exams to appear on the leaderboard!</p>
        <a href="{{ route('mcq.examinations') }}" class="inline-flex items-center space-x-2 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
          <i data-lucide="play" class="w-4 h-4"></i>
          <span>Take an Exam</span>
        </a>
      </div>
    @endif
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});
</script>
@endpush
@endsection
