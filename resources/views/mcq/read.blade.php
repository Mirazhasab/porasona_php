@extends('layouts.mcq')

@section('title', 'Read MCQ Content')
@section('page-title', 'Read MCQ')
@section('page-subtitle', 'Browse and study MCQ materials')

@section('content')
<!-- Stats Grid -->
<div class="stats-grid grid grid-cols-2 gap-1.5 sm:gap-3 lg:grid-cols-4 mb-3 md:mb-6">
    <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-700">Total MCQ Sets</p>
                <p class="stat-number font-bold text-gray-800">{{ $stats['totalSets'] }}</p>
                <div class="stat-trend flex items-center">
                    <i data-lucide="book-open" class="w-3 h-3 text-teal-500 mr-1"></i>
                    <span class="text-xs text-gray-700">Available</span>
                </div>
            </div>
            <div class="stat-icon bg-teal-100 border border-teal-300 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i data-lucide="book-open" class="text-teal-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-700">Total Questions</p>
                <p class="stat-number font-bold text-gray-800">{{ $stats['totalQuestions'] }}</p>
                <div class="stat-trend flex items-center">
                    <i data-lucide="help-circle" class="w-3 h-3 text-blue-500 mr-1"></i>
                    <span class="text-xs text-gray-700">Questions</span>
                </div>
            </div>
            <div class="stat-icon bg-blue-100 border border-blue-300 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i data-lucide="help-circle" class="text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-700">Categories</p>
                <p class="stat-number font-bold text-gray-800">{{ $stats['categories']->count() }}</p>
                <div class="stat-trend flex items-center">
                    <i data-lucide="folder" class="w-3 h-3 text-purple-500 mr-1"></i>
                    <span class="text-xs text-gray-700">Topics</span>
                </div>
            </div>
            <div class="stat-icon bg-purple-100 border border-purple-300 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i data-lucide="folder" class="text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-700">Recently Added</p>
                <p class="stat-number font-bold text-gray-800">{{ $stats['recentlyAdded'] }}</p>
                <div class="stat-trend flex items-center">
                    <i data-lucide="clock" class="w-3 h-3 text-green-500 mr-1"></i>
                    <span class="text-xs text-gray-700">This week</span>
                </div>
            </div>
            <div class="stat-icon bg-green-100 border border-green-300 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i data-lucide="clock" class="text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Categories Filter -->
@if($stats['categories']->count() > 0)
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-0 md:mb-6">
    <div class="p-0 md:p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Browse by Category</h3>
    </div>
    <div class="p-0 md:p-6">
        <div class="flex flex-wrap gap-2 sm:gap-3">
            <a href="{{ route('mcq.read') }}" class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-800 text-sm rounded-lg hover:bg-blue-200 transition-colors">
                <i data-lucide="grid-3x3" class="w-4 h-4 mr-2"></i>
                All Categories
            </a>
            @foreach($stats['categories'] as $category)
                <a href="{{ route('mcq.read', ['category' => $category]) }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                    <i data-lucide="tag" class="w-4 h-4 mr-2"></i>
                    {{ ucfirst($category) }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- MCQ Sets Grid -->
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
    <div class="p-0 md:p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">MCQ Study Materials</h3>
        <p class="text-sm text-gray-600 mt-1">Browse and study available MCQ sets</p>
    </div>
    <div class="p-0 md:p-6">
        @if($mcqSets->count() > 0)
            <div class="grid grid-cols-1 gap-0 sm:gap-4 md:grid-cols-2 lg:grid-cols-3 md:gap-6">
                @foreach($mcqSets as $mcqSet)
                    @php
                        $questionCount = $mcqSet->questions_count ?? $mcqSet->questions->count();
                    @endphp
                    <div class="mcq-set-card bg-white border border-gray-200 rounded-xl p-0 md:p-6 hover:border-teal-300 hover:shadow-lg transition-all duration-300">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-0 md:mb-4">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 mb-2 line-clamp-2">{{ $mcqSet->title }}</h4>
                                @if($mcqSet->category)
                                    <span class="inline-flex items-center px-2 py-1 bg-teal-100 text-teal-800 text-xs rounded-full">
                                        <i data-lucide="tag" class="w-3 h-3 mr-1"></i>
                                        {{ ucfirst($mcqSet->category) }}
                                    </span>
                                @endif
                            </div>
                            <div class="ml-3">
                                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                                    <i data-lucide="book-open" class="w-6 h-6 text-teal-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($mcqSet->description)
                            <p class="text-sm text-gray-600 mb-0 md:mb-4 line-clamp-3">{{ $mcqSet->description }}</p>
                        @endif

                        <!-- Stats -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-0 md:mb-4">
                            <div class="flex items-center">
                                <i data-lucide="help-circle" class="w-4 h-4 mr-1"></i>
                                    <span>{{ number_format($questionCount) }} {{ \Illuminate\Support\Str::plural('Question', $questionCount) }}</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="calendar" class="w-4 h-4 mr-1"></i>
                                <span>{{ $mcqSet->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <!-- Sample Questions Preview -->
                        @if($questionCount > 0)
                            <div class="border-t border-gray-200 pt-0 md:pt-4 mb-0 md:mb-4">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Sample Questions:</h5>
                                <div class="space-y-2">
                                    @foreach($mcqSet->questions->take(2) as $question)
                                        <div class="text-xs text-gray-600 bg-gray-50 p-2 rounded">
                                            <span class="font-medium">Q:</span> {{ Str::limit($question->question, 80) }}
                                        </div>
                                    @endforeach
                                    @if($questionCount > 2)
                                        <div class="text-xs text-gray-500 italic">
                                            +{{ number_format($questionCount - 2) }} more questions...
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('mcq.read.content', $mcqSet->id) }}" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-teal-700 transition-colors text-center">
                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                Read Content
                            </a>
                            @if(auth()->user()->access && auth()->user()->access->exams)
                                <a href="{{ route('mcq.examinations') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors" title="Take Exam">
                                    <i data-lucide="play" class="w-4 h-4 inline"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($mcqSets->hasPages())
                <div class="mt-4 md:mt-8 flex justify-center">
                    {{ $mcqSets->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-10 md:py-12">
                <div class="w-20 h-20 md:w-24 md:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                    <i data-lucide="book-open" class="w-12 h-12 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No MCQ Sets Available</h3>
                <p class="text-gray-500 mb-3 md:mb-4">There are no approved MCQ sets available for reading at the moment.</p>
                @if($isAdmin)
                    <a href="{{ route('mcq_sets.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Create MCQ Set
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .mcq-set-card {
        transition: all 0.3s ease;
    }
    
    .mcq-set-card:hover {
        transform: translateY(-2px);
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
