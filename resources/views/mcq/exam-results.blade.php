@extends('layouts.mcq')

@section('title', 'Exam Results - MCQ System')
@section('page-title', 'Exam Results')
@section('page-subtitle', 'Your performance summary')

@section('content')
<div class="max-w-5xl mx-auto">
  {{-- Back Button --}}
  <div class="mb-6">
    <a href="{{ route('mcq.examinations') }}" class="text-primary hover:text-secondary flex items-center">
      <i class="fas fa-arrow-left mr-2"></i>Back to Examinations
    </a>
  </div>

  {{-- Results Header --}}
  <div class="bg-white rounded-xl shadow-lg border-2 {{ $statistics['is_passed'] ? 'border-green-500' : 'border-red-500' }} p-8 mb-6">
    <div class="text-center">
      @if($statistics['is_passed'])
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-trophy text-green-600 text-5xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-green-600 mb-2">Congratulations! You Passed!</h2>
        <p class="text-gray-600">You have successfully completed the examination</p>
      @else
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-times-circle text-red-600 text-5xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-red-600 mb-2">Better Luck Next Time</h2>
        <p class="text-gray-600">Keep practicing to improve your score</p>
      @endif
    </div>

    {{-- Score Display --}}
    <div class="mt-8 text-center">
      <div class="inline-block bg-gray-50 rounded-xl p-8">
        <p class="text-gray-600 mb-2">Your Score</p>
        <div class="flex items-center justify-center space-x-3">
          <span class="text-6xl font-bold {{ $statistics['is_passed'] ? 'text-green-600' : 'text-red-600' }}">
            {{ $statistics['percentage'] }}%
          </span>
        </div>
        <p class="text-gray-600 mt-2">{{ $statistics['obtained_marks'] }} / {{ $statistics['total_marks'] }} marks</p>
      </div>
    </div>
  </div>

  {{-- Statistics Grid --}}
  <div class="grid grid-cols-2 md:grid-cols-{{ $statistics['unanswered_questions'] > 0 ? '5' : '4' }} gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
      <i class="fas fa-question-circle text-blue-600 text-3xl mb-3"></i>
      <p class="text-sm text-gray-600 mb-1">Total Questions</p>
      <p class="text-3xl font-bold text-gray-800">{{ $statistics['total_questions'] }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
      <i class="fas fa-check-circle text-green-600 text-3xl mb-3"></i>
      <p class="text-sm text-gray-600 mb-1">Correct Answers</p>
      <p class="text-3xl font-bold text-green-600">{{ $statistics['correct_answers'] }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
      <i class="fas fa-times-circle text-red-600 text-3xl mb-3"></i>
      <p class="text-sm text-gray-600 mb-1">Wrong Answers</p>
      <p class="text-3xl font-bold text-red-600">{{ $statistics['wrong_answers'] }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
      <i class="fas fa-chart-line text-purple-600 text-3xl mb-3"></i>
      <p class="text-sm text-gray-600 mb-1">Accuracy</p>
      <p class="text-3xl font-bold text-purple-600">{{ $statistics['accuracy'] }}%</p>
      <p class="text-xs text-gray-500 mt-1">{{ $statistics['answered_questions'] }}/{{ $statistics['total_questions'] }} answered</p>
    </div>

    @if($statistics['unanswered_questions'] > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
      <i class="fas fa-question text-orange-600 text-3xl mb-3"></i>
      <p class="text-sm text-gray-600 mb-1">Unanswered</p>
      <p class="text-3xl font-bold text-orange-600">{{ $statistics['unanswered_questions'] }}</p>
    </div>
    @endif
  </div>

  {{-- Exam Details --}}
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">
      <i class="fas fa-file-alt mr-2 text-primary"></i>{{ $mcqSet->title ?? $mcqSet->exam_name }}
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
      <div>
        <span class="text-gray-600">Exam Name:</span>
        <p class="font-semibold text-gray-800">{{ $mcqSet->exam_name }}</p>
      </div>
      @if($mcqSet->exam_date)
      <div>
        <span class="text-gray-600">Exam Date:</span>
        <p class="font-semibold text-gray-800">{{ $mcqSet->exam_date->format('M d, Y') }}</p>
      </div>
      @endif
      <div>
        <span class="text-gray-600">Duration:</span>
        <p class="font-semibold text-gray-800">{{ $mcqSet->duration ?? 60 }} minutes</p>
      </div>
    </div>
  </div>

  {{-- Detailed Answers Review --}}
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <h3 class="text-2xl font-bold text-gray-800 mb-6">
      <i class="fas fa-list-check mr-2 text-primary"></i>Detailed Review
    </h3>

    <div class="space-y-6">
      @foreach($mcqSet->questions as $index => $question)
        @php
          $userAnswer = $userAnswers->get($question->id);
          $isCorrect = $userAnswer && $userAnswer->is_correct;
          $selectedAnswer = $userAnswer ? $userAnswer->selected_answer : null;
          $isAnswered = $userAnswer !== null;
          
          // Determine border and background colors
          if (!$isAnswered) {
            $borderClass = 'border-orange-200 bg-orange-50';
            $statusClass = 'bg-orange-600';
            $statusText = 'Not Answered';
            $statusIcon = 'fa-question';
          } elseif ($isCorrect) {
            $borderClass = 'border-green-200 bg-green-50';
            $statusClass = 'bg-green-600';
            $statusText = 'Correct';
            $statusIcon = 'fa-check';
          } else {
            $borderClass = 'border-red-200 bg-red-50';
            $statusClass = 'bg-red-600';
            $statusText = 'Wrong';
            $statusIcon = 'fa-times';
          }
        @endphp

        <div class="border-2 {{ $borderClass }} rounded-lg p-6">
          <div class="flex items-start mb-4">
            <span class="flex-shrink-0 w-10 h-10 {{ $statusClass }} text-white rounded-full flex items-center justify-center font-bold text-lg mr-4">
              {{ $index + 1 }}
            </span>
            <div class="flex-1">
              <div class="flex items-start justify-between mb-3">
                <p class="text-gray-800 font-semibold text-lg flex-1">{{ $question->question }}</p>
                <span class="ml-4 px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }} text-white">
                  <i class="fas {{ $statusIcon }} mr-1"></i>
                  {{ $statusText }}
                </span>
              </div>
              
              <div class="space-y-3">
                @foreach(['1' => $question->ans_1, '2' => $question->ans_2, '3' => $question->ans_3, '4' => $question->ans_4] as $key => $answer)
                  @php
                    $isUserSelection = ($selectedAnswer == $key);
                    $isCorrectAnswer = ($question->correct_ans == $key);
                  @endphp

                  <div class="p-4 border-2 rounded-lg
                    @if($isCorrectAnswer && $isUserSelection)
                      border-green-500 bg-green-100
                    @elseif($isCorrectAnswer)
                      border-green-500 bg-green-50
                    @elseif($isUserSelection)
                      border-red-500 bg-red-100
                    @else
                      border-gray-200 bg-white
                    @endif">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center flex-1">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm font-bold
                          @if($isCorrectAnswer)
                            bg-green-600 text-white
                          @elseif($isUserSelection)
                            bg-red-600 text-white
                          @else
                            bg-gray-200 text-gray-700
                          @endif">
                          {{ chr(64 + $key) }}
                        </span>
                        <span class="text-gray-800 font-medium">{{ $answer }}</span>
                      </div>
                      <div class="flex items-center space-x-2">
                        @if($isUserSelection)
                          <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-semibold">
                            Your Answer
                          </span>
                        @endif
                        @if($isCorrectAnswer)
                          <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        @endif
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>

              {{-- Question Notes/Explanation --}}
              @if($question->notes)
              <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-900">
                  <i class="fas fa-lightbulb mr-2 text-blue-600"></i>
                  <strong>Explanation:</strong> {{ $question->notes }}
                </p>
              </div>
              @endif

              {{-- Marks --}}
              <div class="mt-3 flex items-center justify-between text-sm">
                <span class="text-gray-600">
                  <i class="fas fa-star text-yellow-500 mr-1"></i>
                  Question Marks: {{ $question->marks ?? 1 }}
                </span>
                <span class="font-semibold 
                  @if(!$isAnswered) 
                    text-orange-600
                  @elseif($isCorrect) 
                    text-green-600 
                  @else 
                    text-red-600 
                  @endif">
                  You scored: {{ $userAnswer->marks_obtained ?? 0 }} marks
                </span>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Action Buttons --}}
  <div class="mt-8 flex justify-center space-x-4">
    <a href="{{ route('mcq.examinations') }}" 
       class="px-8 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
      <i class="fas fa-list mr-2"></i>Back to Examinations
    </a>
    <a href="{{ route('mcq.dashboard') }}" 
       class="px-8 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition-colors">
      <i class="fas fa-home mr-2"></i>Go to Dashboard
    </a>
  </div>

  {{-- Performance Message --}}
  <div class="mt-6 text-center text-gray-600">
    <p>Exam completed on {{ $userAnswers->first()->created_at->format('M d, Y \a\t h:i A') }}</p>
  </div>
</div>
@endsection
