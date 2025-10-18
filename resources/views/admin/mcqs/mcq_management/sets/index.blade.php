@extends('layouts.mcq')

@section('title', 'MCQ Management')
@section('page-title', 'MCQ Management')
@section('page-subtitle', 'Manage your MCQ Sets')

@section('content')
<div class="container mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">MCQ Sets</h1>
        <div class="flex gap-3">
            @if(auth()->user()->hasRole('admin') || auth()->user()->role === 'admin')
            <a href="{{ route('mcq_sets.show-import') }}" wire:navigate class="bg-white hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg transition-colors flex items-center gap-2" style="border: 1px solid #cbd5e1;">
                <i class="fas fa-upload"></i>
                <span>Import from SQLite3</span>
            </a>
            @endif
            <a href="{{ route('mcq_sets.create') }}" wire:navigate class="bg-white hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg transition-colors flex items-center gap-2" style="border: 1px solid #cbd5e1;">
                <i class="fas fa-plus"></i>
                <span>Create New MCQ Set</span>
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 bg-gray-100 border-2 border-gray-300 text-gray-800 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-gray-100 border-2 border-gray-300 text-gray-800 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- MCQ Sets List -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($mcqSets as $set)
            <div class="bg-white rounded-xl border-2 border-gray-300 hover:shadow-lg transition-shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $set->title }}</h3>
                        
                        @if($set->exam_name)
                            <p class="text-gray-600 mb-1">
                                <i class="fas fa-book mr-2"></i>
                                <strong>Exam:</strong> {{ $set->exam_name }}
                            </p>
                        @endif
                        
                        @if($set->exam_date)
                            <p class="text-gray-600 mb-1">
                                <i class="fas fa-calendar mr-2"></i>
                                <strong>Date:</strong> {{ $set->exam_date->format('F d, Y') }}
                            </p>
                        @endif
                        
                        @if($set->duration)
                            <p class="text-gray-600 mb-1">
                                <i class="fas fa-clock mr-2"></i>
                                <strong>Duration:</strong> {{ $set->duration }} minutes
                            </p>
                        @endif
                        
                        <p class="text-gray-600 mb-1">
                            <i class="fas fa-question-circle mr-2"></i>
                            <strong>Questions:</strong> {{ $set->questions->count() }}
                        </p>
                        
                        <p class="text-gray-600 mb-1">
                            <i class="fas fa-user mr-2"></i>
                            <strong>Created by:</strong> {{ $set->user->name }}
                        </p>
                    </div>
                    
                    <div class="ml-4">
                        @if($set->status === 'pending')
                            <span class="bg-gray-100 border border-gray-300 text-gray-800 text-sm px-3 py-1 rounded-full">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                        @elseif($set->status === 'approved')
                            <span class="bg-gray-100 border border-gray-300 text-gray-800 text-sm px-3 py-1 rounded-full">
                                <i class="fas fa-check mr-1"></i>Approved
                            </span>
                        @else
                            <span class="bg-gray-100 border border-gray-300 text-gray-800 text-sm px-3 py-1 rounded-full">
                                <i class="fas fa-times mr-1"></i>Rejected
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('mcq_sets.show', $set) }}" wire:navigate class="bg-white hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors text-sm" style="border: 1px solid #cbd5e1;">
                        <i class="fas fa-eye mr-1"></i>View Details
                    </a>
                    
                    @if(auth()->user()->hasRole('admin') || auth()->user()->role === 'admin')
                    <a href="{{ route('mcq_sets.show-import', ['set_id' => $set->id]) }}" wire:navigate class="bg-white hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors text-sm" style="border: 1px solid #cbd5e1;">
                        <i class="fas fa-upload mr-1"></i>Import Questions
                    </a>
                    @endif
                    
                    @if((auth()->user()->hasRole('admin') || auth()->user()->role === 'admin') || ($set->user_id === auth()->id() && $set->status !== 'approved'))
                        <a href="{{ route('mcq_sets.edit', $set) }}" wire:navigate class="bg-white hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors text-sm" style="border: 1px solid #cbd5e1;">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        
                        <form action="{{ route('mcq_sets.destroy', $set) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this MCQ set?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </form>
                    @endif
                    
                    @if((auth()->user()->hasRole('admin') || auth()->user()->role === 'admin') && $set->status === 'pending')
                        <form action="{{ route('mcq_sets.approve', $set->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-white hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors text-sm" style="border: 1px solid #cbd5e1;">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                        </form>
                        
                        <form action="{{ route('mcq_sets.reject', $set->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                <i class="fas fa-times mr-1"></i>Reject
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border-2 border-gray-300 p-8 text-center">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No MCQ sets found.</p>
                <a href="{{ route('mcq_sets.create') }}" wire:navigate class="mt-4 inline-block bg-white hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg transition-colors" style="border: 1px solid #cbd5e1;">
                    Create Your First MCQ Set
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $mcqSets->links() }}
    </div>
</div>
@endsection
