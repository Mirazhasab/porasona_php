@extends('layouts.mcq')

@section('title', 'My Results - MCQ Pro')
@section('page-title', $isAdmin ? 'Analytics' : 'My Results')
@section('page-subtitle', $isAdmin ? 'Performance insights and reports' : 'Track your exam performance')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6">
  <div class="bg-white  rounded-2xl p-4 border-2 border-gray-300">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-600 ">Total Exams</p>
        <p class="text-xl lg:text-2xl font-bold text-gray-900  mt-1">{{ $stats['totalExams'] }}</p>
      </div>
      <div class="w-10 h-10 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center">
        <i data-lucide="clipboard-list" class="w-5 h-5 text-gray-700"></i>
      </div>
    </div>
  </div>
  
  <div class="bg-white  rounded-2xl p-4 border-2 border-gray-300">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-600 ">Passed</p>
        <p class="text-xl lg:text-2xl font-bold text-gray-900  mt-1">{{ $stats['passedExams'] }}</p>
      </div>
      <div class="w-10 h-10 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center">
        <i data-lucide="check-circle" class="w-5 h-5 text-gray-700"></i>
      </div>
    </div>
  </div>
  
  <div class="bg-white  rounded-2xl p-4 border-2 border-gray-300">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-600 ">Average</p>
        <p class="text-xl lg:text-2xl font-bold text-gray-900  mt-1">{{ round($stats['averageScore'], 1) }}%</p>
      </div>
      <div class="w-10 h-10 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center">
        <i data-lucide="bar-chart-3" class="w-5 h-5 text-gray-700"></i>
      </div>
    </div>
  </div>
  
  <div class="bg-white  rounded-2xl p-4 border-2 border-gray-300">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-600 ">Best Score</p>
        <p class="text-xl lg:text-2xl font-bold text-gray-900  mt-1">{{ round($stats['bestScore'], 1) }}%</p>
      </div>
      <div class="w-10 h-10 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center">
        <i data-lucide="trophy" class="w-5 h-5 text-gray-700"></i>
      </div>
    </div>
  </div>
</div>

<!-- Exam Results -->
<div class="bg-white  rounded-2xl border-2 border-gray-300">
  <div class="p-4 lg:p-6 border-b border-gray-200 ">
    <h3 class="text-xl font-bold text-gray-900 ">My Exam Results</h3>
    <p class="text-sm text-gray-600  mt-1">Your performance history</p>
  </div>
  
  <div class="p-4 lg:p-6">
    @if(count($examResults) > 0)
      <!-- Desktop Table -->
      <div class="hidden lg:block overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200 ">
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Exam</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Questions</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Correct</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Score</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Status</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Date</th>
            </tr>
          </thead>
          <tbody>
            @foreach($examResults as $result)
            <tr class="border-b border-gray-100  hover:bg-gray-50 ">
              <td class="py-4 px-4">
                <div class="font-medium text-gray-900 ">{{ $result['exam'] }}</div>
              </td>
              <td class="py-4 px-4 text-gray-600 ">{{ $result['total_questions'] }}</td>
              <td class="py-4 px-4 text-gray-600 ">{{ $result['correct_answers'] }}</td>
              <td class="py-4 px-4">
                <span class="text-lg font-bold text-gray-800">
                  {{ $result['percentage'] }}%
                </span>
              </td>
              <td class="py-4 px-4">
                <span class="px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 border border-gray-300 text-gray-800">
                  {{ $result['status'] }}
                </span>
              </td>
              <td class="py-4 px-4 text-gray-500 ">{{ $result['date'] }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <!-- Mobile Cards -->
      <div class="lg:hidden space-y-4">
        @foreach($examResults as $result)
        <div class="border-2 border-gray-300 rounded-xl p-4">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
              <h4 class="font-medium text-gray-900  truncate">{{ $result['exam'] }}</h4>
              <p class="text-xs text-gray-500  mt-1">{{ $result['date'] }}</p>
            </div>
            <span class="px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 border border-gray-300 text-gray-800">
              {{ $result['status'] }}
            </span>
          </div>
          <div class="grid grid-cols-3 gap-4 text-center">
            <div>
              <p class="text-xs text-gray-500 ">Questions</p>
              <p class="text-sm font-medium text-gray-900 ">{{ $result['total_questions'] }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 ">Correct</p>
              <p class="text-sm font-medium text-gray-900 ">{{ $result['correct_answers'] }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 ">Score</p>
              <p class="text-lg font-bold text-gray-800">
                {{ $result['percentage'] }}%
              </p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-12">
        <i data-lucide="bar-chart-3" class="w-12 h-12 text-gray-400  mx-auto mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900  mb-2">No Results Yet</h3>
        <p class="text-gray-600  mb-4">You haven't taken any exams yet. Start practicing to see your results here!</p>
        <a href="{{ route('mcq.examinations') }}" class="inline-flex items-center space-x-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
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
