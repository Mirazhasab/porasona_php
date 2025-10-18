@extends('layouts.mcq')

@section('title', 'Edit MCQ Set')
@section('page-title', 'Edit MCQ Set')
@section('page-subtitle', 'Update MCQ set details')

@section('content')
<div class="container mx-auto max-w-3xl">
    <div class="bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit MCQ Set</h2>
        
        <form action="{{ route('mcq_sets.update', $mcqSet) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $mcqSet->title) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="e.g., BCS 10th MCQ"
                       required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Exam Name -->
            <div class="mb-6">
                <label for="exam_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Exam Name
                </label>
                <input type="text" 
                       name="exam_name" 
                       id="exam_name" 
                       value="{{ old('exam_name', $mcqSet->exam_name) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
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
                           value="{{ old('exam_date', $mcqSet->exam_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
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
                           value="{{ old('exam_time', $mcqSet->exam_time instanceof \Carbon\Carbon ? $mcqSet->exam_time->format('H:i') : $mcqSet->exam_time) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
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
                           value="{{ old('total_marks', $mcqSet->total_marks) }}"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
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
                           value="{{ old('duration', $mcqSet->duration) }}"
                           min="1"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="60">
                    @error('duration')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-white hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg transition-colors font-medium" style="border: 1px solid #cbd5e1;">
                        <i class="fas fa-save mr-2"></i>Update MCQ Set
                    </button>
                    <a href="{{ route('mcq_sets.show', $mcqSet) }}" wire:navigate class="flex-1 bg-white hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg transition-colors font-medium text-center" style="border: 1px solid #cbd5e1;">
                        <i class="fas fa-times mr-2 text-gray-800"></i>Cancel
                    </a>
            </div>
        </form>
    </div>
</div>
@endsection
