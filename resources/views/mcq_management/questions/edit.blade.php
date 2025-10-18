@extends('layouts.mcq')

@section('title', 'Edit Question')
@section('page-title', 'Edit Question')
@section('page-subtitle', 'Update question details')

@section('content')
<div class="container mx-auto max-w-3xl">
    <div class="bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Edit Question</h2>
        <p class="text-gray-600 mb-6">MCQ Set: <strong>{{ $mcqQuestion->mcqSet->title }}</strong></p>
        
        <form action="{{ route('mcq_questions.update', $mcqQuestion) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Question -->
            <div class="mb-6">
                <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                    Question <span class="text-red-500">*</span>
                </label>
                <textarea name="question" 
                          id="question" 
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="Enter your question here..."
                          required>{{ old('question', $mcqQuestion->question) }}</textarea>
                @error('question')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Answer Options -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Answer Options <span class="text-red-500">*</span>
                </label>
                
                <div class="space-y-3">
                    <!-- Answer 1 -->
                    <div class="answer-input-container">
                        <span class="answer-letter-badge">A</span>
                        <input type="text" 
                               name="ans_1" 
                               value="{{ old('ans_1', $mcqQuestion->ans_1) }}"
                               class="answer-input-field"
                               placeholder="First answer option"
                               required>
                    </div>
                    @error('ans_1')
                        <p class="text-red-500 text-sm mt-1 ml-11">{{ $message }}</p>
                    @enderror
                    
                    <!-- Answer 2 -->
                    <div class="answer-input-container">
                        <span class="answer-letter-badge">B</span>
                        <input type="text" 
                               name="ans_2" 
                               value="{{ old('ans_2', $mcqQuestion->ans_2) }}"
                               class="answer-input-field"
                               placeholder="Second answer option"
                               required>
                    </div>
                    @error('ans_2')
                        <p class="text-red-500 text-sm mt-1 ml-11">{{ $message }}</p>
                    @enderror
                    
                    <!-- Answer 3 -->
                    <div class="answer-input-container">
                        <span class="answer-letter-badge">C</span>
                        <input type="text" 
                               name="ans_3" 
                               value="{{ old('ans_3', $mcqQuestion->ans_3) }}"
                               class="answer-input-field"
                               placeholder="Third answer option"
                               required>
                    </div>
                    @error('ans_3')
                        <p class="text-red-500 text-sm mt-1 ml-11">{{ $message }}</p>
                    @enderror
                    
                    <!-- Answer 4 -->
                    <div class="answer-input-container">
                        <span class="answer-letter-badge">D</span>
                        <input type="text" 
                               name="ans_4" 
                               value="{{ old('ans_4', $mcqQuestion->ans_4) }}"
                               class="answer-input-field"
                               placeholder="Fourth answer option"
                               required>
                    </div>
                    @error('ans_4')
                        <p class="text-red-500 text-sm mt-1 ml-11">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Correct Answer -->
            <div class="mb-6">
                <label for="correct_ans" class="block text-sm font-medium text-gray-700 mb-2">
                    Correct Answer <span class="text-red-500">*</span>
                </label>
                <select name="correct_ans" 
                        id="correct_ans" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        required>
                    <option value="">Select correct answer</option>
                    <option value="1" {{ old('correct_ans', $mcqQuestion->correct_ans) == '1' ? 'selected' : '' }}>A (First answer)</option>
                    <option value="2" {{ old('correct_ans', $mcqQuestion->correct_ans) == '2' ? 'selected' : '' }}>B (Second answer)</option>
                    <option value="3" {{ old('correct_ans', $mcqQuestion->correct_ans) == '3' ? 'selected' : '' }}>C (Third answer)</option>
                    <option value="4" {{ old('correct_ans', $mcqQuestion->correct_ans) == '4' ? 'selected' : '' }}>D (Fourth answer)</option>
                </select>
                @error('correct_ans')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Notes/Explanation -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes/Explanation (Optional)
                </label>
                <textarea name="notes" 
                          id="notes" 
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="Add explanation or hints for this question...">{{ old('notes', $mcqQuestion->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Marks -->
            <div class="mb-6">
                <label for="marks" class="block text-sm font-medium text-gray-700 mb-2">
                    Marks <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="marks" 
                       id="marks" 
                       value="{{ old('marks', $mcqQuestion->marks) }}"
                       min="1"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       required>
                @error('marks')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg transition-colors font-medium">
                    <i class="fas fa-save mr-2"></i>Update Question
                </button>
                <a href="{{ route('mcq_sets.show', $mcqQuestion->mcqSet) }}" wire:navigate class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg transition-colors font-medium text-center">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
