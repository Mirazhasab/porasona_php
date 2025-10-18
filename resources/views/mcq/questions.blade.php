@extends('layouts.mcq')

@section('title', $isAdmin ? 'Question Bank - MCQ Pro' : 'Practice Questions - MCQ Pro')
@section('page-title', $isAdmin ? 'Question Bank' : 'Practice Questions')
@section('page-subtitle', $isAdmin ? 'Manage MCQ questions' : 'Practice and improve your skills')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6">
  <div class="bg-white  rounded-2xl p-4 border-2 border-gray-300">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-600 ">Total Questions</p>
        <p class="text-xl lg:text-2xl font-bold text-gray-900  mt-1">{{ $stats['totalQuestions'] }}</p>
      </div>
      <div class="w-10 h-10 bg-gray-100 border-2 border-gray-300 rounded-xl flex items-center justify-center">
        <i data-lucide="help-circle" class="w-5 h-5 text-gray-600"></i>
      </div>
    </div>
  </div>
  
  <div class="bg-white  rounded-2xl p-4 border-2 border-gray-300">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-600 ">Available</p>
        <p class="text-xl lg:text-2xl font-bold text-gray-900  mt-1">{{ $stats['practiceQuestions'] }}</p>
      </div>
      <div class="w-10 h-10 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center">
        <i data-lucide="check-circle" class="w-5 h-5 text-gray-700"></i>
      </div>
    </div>
  </div>
  
  <div class="bg-white  rounded-2xl p-4 border-2 border-gray-300">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-600 ">My Attempts</p>
        <p class="text-xl lg:text-2xl font-bold text-gray-900  mt-1">{{ $stats['myAttempts'] }}</p>
      </div>
      <div class="w-10 h-10 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center">
        <i data-lucide="target" class="w-5 h-5 text-gray-700"></i>
      </div>
    </div>
  </div>
  
  <div class="bg-white  rounded-2xl p-4 border-2 border-gray-300">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-600 ">Correct</p>
        <p class="text-xl lg:text-2xl font-bold text-gray-900  mt-1">{{ $stats['correctAnswers'] }}</p>
      </div>
      <div class="w-10 h-10 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center">
        <i data-lucide="star" class="w-5 h-5 text-gray-700"></i>
      </div>
    </div>
  </div>
</div>

<!-- Header -->
<div class="bg-white  rounded-2xl border-2 border-gray-300 mb-6">
  <div class="p-4 lg:p-6 border-b border-gray-200 ">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
      <div>
        <h3 class="text-xl font-bold text-gray-900 ">
          @if($isAdmin) Question Management @else Practice Questions @endif
        </h3>
        <p class="text-sm text-gray-600  mt-1">
          @if($isAdmin) Manage all questions in the system @else Practice questions to improve your skills @endif
        </p>
      </div>
      @if($isAdmin)
      <button class="inline-flex items-center space-x-2 bg-gray-600 border-2 border-gray-600 hover:bg-gray-700 hover:border-gray-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Add Question</span>
      </button>
      @endif
    </div>
  </div>

  <!-- Questions List -->
  <div class="p-4 lg:p-6">
    @if($questionsQuery->count() > 0)
      <!-- Desktop Table -->
      <div class="hidden lg:block overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200 ">
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Question</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Set</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Type</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 ">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($questionsQuery as $question)
            <tr class="border-b border-gray-100  hover:bg-gray-50 ">
              <td class="py-4 px-4">
                <div class="max-w-md">
                  <p class="text-sm font-medium text-gray-900  line-clamp-2">{{ $question->question }}</p>
                </div>
              </td>
              <td class="py-4 px-4">
                <span class="bg-gray-100 border border-gray-300 text-gray-800 px-2 py-1 rounded-lg text-xs font-medium">
                  {{ $question->mcqSet->title ?? 'General' }}
                </span>
              </td>
              <td class="py-4 px-4">
                <span class="bg-gray-100 border border-gray-300 text-gray-700 px-2 py-1 rounded-lg text-xs font-medium">
                  MCQ
                </span>
              </td>
              <td class="py-4 px-4">
                <div class="flex items-center space-x-2">
                  @if($isAdmin)
                    <button class="p-2 text-gray-600 hover:bg-gray-50 border border-gray-300 rounded-lg" title="Edit">
                      <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </button>
                    <button class="p-2 text-red-600  hover:bg-red-50  rounded-lg" title="Delete">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  @else
                    <button class="inline-flex items-center space-x-1 text-gray-700 hover:text-gray-800 text-sm font-medium">
                      <i data-lucide="play" class="w-4 h-4"></i>
                      <span>Practice</span>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <!-- Mobile Cards -->
      <div class="lg:hidden space-y-3">
        @foreach($questionsQuery as $question)
        <div class="border-2 border-gray-300 rounded-xl p-4 hover:shadow-lg transition-all duration-200">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900  line-clamp-2">{{ $question->question }}</p>
              <p class="text-xs text-gray-500  mt-1">{{ $question->mcqSet->title ?? 'General' }}</p>
            </div>
            <span class="bg-gray-100 border border-gray-300 text-gray-700 px-2 py-1 rounded-lg text-xs font-medium ml-2">
              MCQ
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span class="bg-gray-100 border border-gray-300 text-gray-800 px-2 py-1 rounded-lg text-xs font-medium">
              {{ $question->mcqSet->title ?? 'General' }}
            </span>
            <div class="flex space-x-2">
              @if($isAdmin)
                <button class="p-2 text-gray-600 hover:bg-gray-50 border border-gray-300 rounded-lg">
                  <i data-lucide="edit-2" class="w-4 h-4"></i>
                </button>
                <button class="p-2 text-red-600  hover:bg-red-50  rounded-lg">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              @else
                <button class="inline-flex items-center space-x-1 bg-gray-600 border-2 border-gray-600 hover:bg-gray-700 hover:border-gray-700 text-white px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                  <i data-lucide="play" class="w-3 h-3"></i>
                  <span>Practice</span>
                </button>
              @endif
            </div>
          </div>
        </div>
        @endforeach
      </div>
      
      <!-- Pagination -->
      <div class="mt-6">
        {{ $questionsQuery->links() }}
      </div>
    @else
      <div class="text-center py-12">
        <i data-lucide="help-circle" class="w-12 h-12 text-gray-400  mx-auto mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900  mb-2">No Questions Available</h3>
        <p class="text-gray-600 ">There are no questions available for practice at the moment.</p>
      </div>
    @endif
  </div>
</div>

@if(!$isAdmin)
<div class="mt-6 bg-white border-2 border-gray-300 rounded-xl p-6">
  <div class="flex items-start space-x-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-gray-600 mt-1" style="color: rgb(51, 51, 51); stroke: rgb(51, 51, 51);"><circle cx="12" cy="12" r="10"></circle><path d="m9,9a3,3 0 1 1 5.12,2.12l-2.12,2.12"></path><path d="m12 17h.01"></path></svg>
    <div>
      <h4 class="font-semibold text-gray-800 mb-2">Practice Mode</h4>
      <p class="text-gray-700 text-sm">Click "Practice" to attempt these questions. You can practice as many times as you want to improve your skills!</p>
    </div>
  </div>
</div>
@endif
@endsection
