@extends('layouts.mcq')

@section('title', 'Create MCQ Set')
@section('page-title', 'Create MCQ Set')
@section('page-subtitle', 'Add a new MCQ set')

@section('content')
<div class="container mx-auto max-w-3xl">
    <div class="bg-white rounded-xl border-2 border-gray-300 p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New MCQ Set</h2>
        
        <form action="{{ route('mcq_sets.store') }}" method="POST">
            @php($nonce = bin2hex(random_bytes(16)))
            <input type="hidden" name="_nonce" value="{{ $nonce }}">
            @csrf

            <!-- Title & Category Row -->
            <div class="mb-6 flex flex-col md:flex-row md:gap-6">
                <div class="flex-1 mb-4 md:mb-0">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                           placeholder="e.g., BCS 10th MCQ"
                           required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="category"
                           id="category"
                           value="{{ old('category') }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                           placeholder="e.g., General Knowledge"
                           required>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Exam Name -->
            <div class="mb-6">
                <label for="exam_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Exam Name
                </label>
                <input type="text" 
                       name="exam_name" 
                       id="exam_name" 
                       value="{{ old('exam_name') }}"
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                       placeholder="e.g., Bangladesh Civil Service Exam">
                @error('exam_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Exam Date and Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Date
                    </label>
                    <input type="date" 
                           name="exam_date" 
                           id="exam_date" 
                           value="{{ old('exam_date') }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    @error('exam_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="exam_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Time
                    </label>
                    <input type="time" 
                           name="exam_time" 
                           id="exam_time" 
                           value="{{ old('exam_time') }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    @error('exam_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Total Marks and Duration -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-2">
                        Total Marks
                    </label>
                    <input type="number" 
                           name="total_marks" 
                           id="total_marks" 
                           value="{{ old('total_marks', 0) }}"
                           min="0"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                           placeholder="100">
                    @error('total_marks')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                        Duration (minutes)
                    </label>
                    <input type="number" 
                           name="duration" 
                           id="duration" 
                           value="{{ old('duration') }}"
                           min="1"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                           placeholder="60">
                    @error('duration')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-white hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg transition-colors font-medium" style="border: 1px solid #cbd5e1;">
                    Create MCQ Set
                </button>
                <a href="{{ route('mcq_sets.index') }}" wire:navigate class="flex-1 bg-white hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg transition-colors font-medium text-center" style="border: 1px solid #cbd5e1;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
