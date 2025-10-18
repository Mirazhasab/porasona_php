@extends('layouts.mcq')

@section('title', 'Available Exams')
@section('page-title', 'Available Exams')
@section('page-subtitle', 'Take exams and test your knowledge')

@section('content')
<div class="max-w-7xl mx-auto p-4">
    <!-- Page Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-clipboard-list" style="color: #3b82f6 !important;"></i>
                    Available Exams
                </h1>
                <p class="text-gray-500 mt-1">Take exams and test your knowledge</p>
            </div>
            <div>
                <a href="{{ route('results.index') }}" class="px-4 py-2 border border-blue-500 text-blue-600 rounded-lg font-medium hover:bg-blue-50 transition-colors flex items-center gap-2">
                    <i class="fas fa-chart-line w-4 h-4"></i>
                    My Results
                </a>
            </div>
        </div>
    </div>

    <!-- Available Exams -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-indigo-600">
            <h5 class="font-semibold flex items-center gap-2" style="color: white !important;">
                <i class="fas fa-play-circle"></i>
                Available Exams ({{ $availableExams->total() }})
            </h5>
        </div>
        <div class="p-4">
            @if($availableExams->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($availableExams as $exam)
                        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <h6 class="font-semibold text-lg" style="color: #3b82f6 !important;">{{ $exam->title }}</h6>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Available</span>
                                </div>
                                
                                @if($exam->exam_name)
                                    <p class="text-gray-500 text-sm mb-3 flex items-center gap-1">
                                        <i class="fas fa-graduation-cap"></i>
                                        {{ $exam->exam_name }}
                                    </p>
                                @endif

                                <div class="grid grid-cols-3 gap-2 text-center mb-4">
                                    <div>
                                        <div style="color: #3b82f6 !important;">
                                            <i class="fas fa-question-circle text-lg"></i>
                                        </div>
                                        <small class="text-gray-500">{{ $exam->questions_count ?? 0 }} Questions</small>
                                    </div>
                                    <div>
                                        <div style="color: #f59e0b !important;">
                                            <i class="fas fa-clock text-lg"></i>
                                        </div>
                                        <small class="text-gray-500">
                                            {{ $exam->duration ? $exam->duration . ' min' : 'No limit' }}
                                        </small>
                                    </div>
                                    <div>
                                        <div style="color: #10b981 !important;">
                                            <i class="fas fa-star text-lg"></i>
                                        </div>
                                        <small class="text-gray-500">{{ $exam->total_marks ?? 'N/A' }} Marks</small>
                                    </div>
                                </div>

                                @if($exam->exam_date)
                                    <p class="text-gray-500 text-sm mb-2 flex items-center gap-1">
                                        <i class="fas fa-calendar"></i>
                                        {{ $exam->exam_date->format('M d, Y') }}
                                        @if($exam->exam_time)
                                            at {{ \Carbon\Carbon::parse($exam->exam_time)->format('g:i A') }}
                                        @endif
                                    </p>
                                @endif

                                <p class="text-gray-500 text-sm mb-4 flex items-center gap-1">
                                    <i class="fas fa-user"></i>
                                    Created by {{ $exam->user->name }}
                                </p>
                            </div>
                            <div class="p-4 pt-0">
                                <a href="{{ route('exams.show', $exam) }}" class="w-full px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2" style="background-color: #3b82f6 !important; color: white !important;" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
                                    <i class="fas fa-play w-4 h-4"></i>
                                    Start Exam
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-6">
                    {{ $availableExams->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-6xl text-gray-400 mb-4"></i>
                    <h5 class="text-gray-500 font-semibold text-lg">No Exams Available</h5>
                    <p class="text-gray-400">There are currently no exams available for you to take.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Completed Exams -->
    @if($completedExams->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-emerald-600">
                <h5 class="font-semibold flex items-center gap-2" style="color: white !important;">
                    <i class="fas fa-check-circle"></i>
                    Recently Completed Exams
                </h5>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($completedExams as $exam)
                        <div class="bg-white border-2 border-green-200 rounded-2xl shadow-sm">
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <h6 class="font-semibold">{{ $exam->title }}</h6>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Completed</span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2 text-center mb-4">
                                    <div>
                                        <small class="text-gray-500">Questions</small>
                                        <div class="font-bold">{{ $exam->questions_count ?? 0 }}</div>
                                    </div>
                                    <div>
                                        <small class="text-gray-500">Total Marks</small>
                                        <div class="font-bold">{{ $exam->total_marks ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <a href="{{ route('results.show', $exam) }}" class="w-full px-4 py-2 border border-green-500 text-green-600 rounded-lg font-medium hover:bg-green-50 transition-colors flex items-center justify-center gap-2">
                                    <i class="fas fa-eye w-4 h-4"></i>
                                    View Results
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection