@extends('layouts.mcq')

@section('title', $mcqSet->title)
@section('page-title', $mcqSet->title)
@section('page-subtitle', 'MCQ Set Details')

@section('content')
<div class="container mx-auto">
    <!-- Set Details Card -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $mcqSet->title }}</h2>

                @if($limitedView)
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                        You are viewing a preview of this MCQ set. Subscribe to unlock all {{ $totalQuestions }} questions. Preview limit: {{ min($freeLimit, $totalQuestions) }} questions.
                    </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($mcqSet->exam_name)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-book mr-2 text-primary"></i>
                            <div>
                                <p class="text-xs text-gray-500">Exam Name</p>
                                <p class="font-medium">{{ $mcqSet->exam_name }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($mcqSet->exam_date)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar mr-2 text-primary"></i>
                            <div>
                                <p class="text-xs text-gray-500">Exam Date</p>
                                <p class="font-medium">{{ $mcqSet->exam_date->format('F d, Y') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($mcqSet->exam_time)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-clock mr-2 text-primary"></i>
                            <div>
                                <p class="text-xs text-gray-500">Exam Time</p>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($mcqSet->exam_time)->format('h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-question-circle mr-2 text-primary"></i>
                        <div>
                            <p class="text-xs text-gray-500">Total Questions</p>
                            <p class="font-medium">{{ $totalQuestions }}
                                @if($limitedView)
                                    <span class="block text-xs text-gray-500">Showing {{ $mcqSet->questions->count() }} questions</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($mcqSet->total_marks)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-star mr-2 text-primary"></i>
                            <div>
                                <p class="text-xs text-gray-500">Total Marks</p>
                                <p class="font-medium">{{ $mcqSet->total_marks }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($mcqSet->duration)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-hourglass-half mr-2 text-primary"></i>
                            <div>
                                <p class="text-xs text-gray-500">Duration</p>
                                <p class="font-medium">{{ $mcqSet->duration }} minutes</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-user mr-2 text-primary"></i>
                        <div>
                            <p class="text-xs text-gray-500">Created By</p>
                            <p class="font-medium">{{ $mcqSet->user->name }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2 text-primary"></i>
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="font-medium">
                                @if($mcqSet->status === 'pending')
                                    <span class="text-yellow-600">Pending</span>
                                @elseif($mcqSet->status === 'approved')
                                    <span class="text-green-600">Approved</span>
                                @else
                                    <span class="text-red-600">Rejected</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-2 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('mcq_sets.index') }}" wire:navigate class="bg-white hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors text-sm" style="border: 1px solid #cbd5e1;">
                <i class="fas fa-arrow-left mr-1"></i>Back to List
            </a>
            
            @if(auth()->user()->hasRole('admin') || ($mcqSet->user_id === auth()->id() && $mcqSet->status !== 'approved'))
                <a href="{{ route('mcq_sets.edit', $mcqSet) }}" wire:navigate class="bg-white hover:bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg transition-colors text-sm" style="border: 1px solid #fde68a;">
                    <i class="fas fa-edit mr-1"></i>Edit Set
                </a>
                
                <a href="{{ route('mcq_questions.create', ['mcq_set_id' => $mcqSet->id]) }}" wire:navigate class="bg-white hover:bg-green-100 text-green-700 px-4 py-2 rounded-lg transition-colors text-sm" style="border: 1px solid #bbf7d0;">
                    <i class="fas fa-plus mr-1"></i>Add Question
                </a>
                
                @role('admin')
                <a href="{{ route('mcq_sets.show-import', ['set_id' => $mcqSet->id]) }}" wire:navigate class="bg-white hover:bg-green-100 text-green-700 px-4 py-2 rounded-lg transition-colors text-sm" style="border: 1px solid #bbf7d0;">
                    <i class="fas fa-upload mr-1"></i>Import Questions
                </a>
                @endrole
            @endif
        </div>
    </div>

    <!-- Questions List -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Questions ({{ $mcqSet->questions->count() }})</h3>
        
        @forelse($mcqSet->questions as $index => $question)
            <div class="mb-6 p-4 bg-white rounded-lg border border-gray-200">
                <div class="flex justify-between items-start mb-3">
                    <h4 class="text-lg font-semibold text-gray-800">
                        Question {{ $index + 1 }}
                        <span class="text-sm text-gray-500 font-normal">({{ $question->marks }} mark{{ $question->marks > 1 ? 's' : '' }})</span>
                    </h4>
                    
                    @if(auth()->user()->hasRole('admin') || ($mcqSet->user_id === auth()->id() && $mcqSet->status !== 'approved'))
                        <div class="flex gap-2">
                            <a href="{{ route('mcq_questions.edit', $question) }}" wire:navigate class="text-yellow-600 hover:text-yellow-700 text-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('mcq_questions.destroy', $question) }}" method="POST" class="inline" onsubmit="return confirm('Delete this question?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                
                <p class="text-gray-700 mb-3 font-medium">{{ $question->question }}</p>
                
                <!-- Answer Options -->
                <div class="space-y-2 mb-3">
                    @php
                        $answers = [
                            '1' => $question->ans_1,
                            '2' => $question->ans_2,
                            '3' => $question->ans_3,
                            '4' => $question->ans_4,
                        ];
                        $labels = ['A', 'B', 'C', 'D'];
                    @endphp
                    
                    @foreach($answers as $num => $answer)
                        <div class="flex items-center p-3 rounded {{ $question->correct_ans == $num ? 'bg-green-50 border-2 border-green-300' : 'bg-white border border-gray-200' }}">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold mr-3 {{ $question->correct_ans == $num ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                                {{ $labels[$num - 1] }}
                            </span>
                            <span class="flex-1 {{ $question->correct_ans == $num ? 'font-semibold text-green-800' : 'text-gray-700' }}">
                                {{ $answer }}
                            </span>
                            
                            @if($question->correct_ans == $num)
                                <i class="fas fa-check-circle text-green-600 ml-2 text-lg"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <!-- Notes/Explanation -->
                @if($question->notes)
                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-900">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Explanation:</strong> {{ $question->notes }}
                        </p>
                    </div>
                @endif
                
                <!-- Report (if exists) -->
                @if($question->report)
                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-900">
                            <i class="fas fa-flag mr-2"></i>
                            <strong>Report:</strong> {{ $question->report }}
                        </p>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-8">
                <i class="fas fa-question-circle text-gray-300 text-5xl mb-3"></i>
                <p class="text-gray-500 mb-4">No questions added yet.</p>
                @if(auth()->user()->hasRole('admin') || ($mcqSet->user_id === auth()->id() && $mcqSet->status !== 'approved'))
                    <a href="{{ route('mcq_questions.create', ['mcq_set_id' => $mcqSet->id]) }}" wire:navigate class="bg-white hover:bg-green-100 text-green-700 px-6 py-3 rounded-lg transition-colors inline-block" style="border: 1px solid #bbf7d0;">
                        <i class="fas fa-plus mr-2"></i>Add Your First Question
                    </a>
                @endif
            </div>
        @endforelse

        @if($limitedView)
            <div class="mt-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg text-sm text-indigo-800">
                Want to continue reading? <a href="{{ route('subscriptions.index') }}" class="underline font-semibold">Upgrade your subscription</a> to unlock the remaining questions in this set.
            </div>
        @endif
    </div>
</div>
@endsection
