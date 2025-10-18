@extends('layouts.mcq')

@section('title', 'Add Option')
@section('page-title', 'Add Option')
@section('page-subtitle', 'Add an option to the question')

@section('content')
<div class="container mx-auto max-w-3xl">
    <div class="bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Add New Option</h2>
        <p class="text-gray-600 mb-1"><strong>MCQ Set:</strong> {{ $question->mcqSet->title }}</p>
        <p class="text-gray-600 mb-6"><strong>Question:</strong> {{ Str::limit($question->question, 100) }}</p>
        
        <form action="{{ route('mcq_options.store') }}" method="POST">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">
            
            <!-- Option Text -->
            <div class="mb-6">
                <label for="option_text" class="block text-sm font-medium text-gray-700 mb-2">
                    Option Text <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="option_text" 
                       id="option_text" 
                       value="{{ old('option_text') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="Enter option text..."
                       required>
                @error('option_text')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Is Correct -->
            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="is_correct" 
                           id="is_correct" 
                           value="1"
                           {{ old('is_correct') ? 'checked' : '' }}
                           class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary">
                    <span class="ml-3 text-sm font-medium text-gray-700">
                        This is the correct answer
                    </span>
                </label>
                @error('is_correct')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Information Box -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    If you mark this option as correct, any previously marked correct option will be unmarked automatically.
                </p>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg transition-colors font-medium">
                    <i class="fas fa-save mr-2"></i>Add Option
                </button>
                <a href="{{ route('mcq_sets.show', $question->mcqSet) }}" wire:navigate class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg transition-colors font-medium text-center">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
