@extends('layouts.modern')

@section('title', 'Dashboard - MCQ Pro')
@section('page-title', 'Dashboard')
@section('page-subtitle', $isAdmin ? 'Admin Overview' : 'Welcome back, ' . $user->name)

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6">
    @if($isAdmin)
        <!-- Admin Stats -->
        <div class="bg-white  rounded-2xl p-4 border border-gray-200  hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm font-medium text-blue-800 ">Total Questions</p>
                    <p class="text-xl lg:text-3xl font-bold text-blue-900  mt-1">{{ $stats['totalQuestions'] ?? 0 }}</p>
                    <div class="flex items-center mt-2">
                        <i data-lucide="trending-up" class="w-3 h-3 text-green-500 mr-1"></i>
                        <span class="text-xs text-green-600 ">+12%</span>
                    </div>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 border-2 border-gray-300 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="help-circle" class="w-5 h-5 lg:w-6 lg:h-6 text-blue-800"></i>
                </div>
            </div>
        </div>

        <div class="bg-white  rounded-2xl p-4 border border-gray-200  hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm font-medium text-blue-800 ">Active Exams</p>
                    <p class="text-xl lg:text-3xl font-bold text-blue-900  mt-1">{{ $stats['activeExams'] ?? 0 }}</p>
                    <div class="flex items-center mt-2">
                        <i data-lucide="trending-up" class="w-3 h-3 text-green-500 mr-1"></i>
                        <span class="text-xs text-green-600 ">+8%</span>
                    </div>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100  rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="clipboard-list" class="w-5 h-5 lg:w-6 lg:h-6 text-green-600 "></i>
                </div>
            </div>
        </div>

        <div class="bg-white  rounded-2xl p-4 border border-gray-200  hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm font-medium text-blue-800 ">Students</p>
                    <p class="text-xl lg:text-3xl font-bold text-blue-900  mt-1">{{ $stats['totalStudents'] ?? 0 }}</p>
                    <div class="flex items-center mt-2">
                        <i data-lucide="trending-down" class="w-3 h-3 text-red-500 mr-1"></i>
                        <span class="text-xs text-red-600 ">-3%</span>
                    </div>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100  rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="users" class="w-5 h-5 lg:w-6 lg:h-6 text-purple-600 "></i>
                </div>
            </div>
        </div>

        <div class="bg-white  rounded-2xl p-4 border border-gray-200  hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm font-medium text-blue-800 ">Avg Score</p>
                    <p class="text-xl lg:text-3xl font-bold text-blue-900  mt-1">{{ $stats['avgScore'] ?? 0 }}%</p>
                    <div class="flex items-center mt-2">
                        <i data-lucide="trending-up" class="w-3 h-3 text-green-500 mr-1"></i>
                        <span class="text-xs text-green-600 ">+5%</span>
                    </div>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-100  rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 lg:w-6 lg:h-6 text-yellow-600 "></i>
                </div>
            </div>
        </div>
    @else
        <!-- Student Stats -->
        <div class="bg-white  rounded-2xl p-4 border border-gray-200  hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm font-medium text-blue-800 ">Exams Taken</p>
                    <p class="text-xl lg:text-3xl font-bold text-blue-900  mt-1">{{ $stats['myExamsTaken'] ?? 0 }}</p>
                    <div class="flex items-center mt-2">
                        <i data-lucide="check" class="w-3 h-3 text-blue-800 mr-1"></i>
                        <span class="text-xs text-blue-800">Keep going!</span>
                    </div>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 border-2 border-gray-300 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="clipboard-check" class="w-5 h-5 lg:w-6 lg:h-6 text-blue-800"></i>
                </div>
            </div>
        </div>

        <div class="bg-white  rounded-2xl p-4 border border-gray-200  hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm font-medium text-blue-800 ">My Average</p>
                    <p class="text-xl lg:text-3xl font-bold text-blue-900  mt-1">{{ $stats['myAvgScore'] ?? 0 }}%</p>
                    <div class="flex items-center mt-2">
                        <i data-lucide="star" class="w-3 h-3 text-green-500 mr-1"></i>
                        <span class="text-xs text-green-600 ">Excellent!</span>
                    </div>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100  rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="trophy" class="w-5 h-5 lg:w-6 lg:h-6 text-green-600 "></i>
                </div>
            </div>
        </div>

        <div class="bg-white  rounded-2xl p-4 border border-gray-200  hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm font-medium text-blue-800 ">Available</p>
                    <p class="text-xl lg:text-3xl font-bold text-blue-900  mt-1">{{ $stats['activeExams'] ?? 0 }}</p>
                    <div class="flex items-center mt-2">
                        <i data-lucide="clock" class="w-3 h-3 text-purple-500 mr-1"></i>
                        <span class="text-xs text-purple-600 ">Ready now</span>
                    </div>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100  rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="clipboard-list" class="w-5 h-5 lg:w-6 lg:h-6 text-purple-600 "></i>
                </div>
            </div>
        </div>

        <div class="bg-white  rounded-2xl p-4 border border-gray-200  hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm font-medium text-blue-800 ">My Rank</p>
                    <p class="text-xl lg:text-3xl font-bold text-blue-900  mt-1">#15</p>
                    <div class="flex items-center mt-2">
                        <i data-lucide="trending-up" class="w-3 h-3 text-yellow-500 mr-1"></i>
                        <span class="text-xs text-yellow-600 ">+3 spots</span>
                    </div>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-100  rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="medal" class="w-5 h-5 lg:w-6 lg:h-6 text-yellow-600 "></i>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @if($isAdmin)
        <!-- Admin Quick Actions -->
        <div class="bg-white  rounded-2xl border border-gray-200 ">
            <div class="p-4 border-b border-gray-200 ">
                <h3 class="text-lg font-semibold text-blue-900 ">Quick Actions</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('mcq.examinations') }}" class="group p-4 border-2 border-dashed border-gray-300  rounded-xl hover:border-gray-500 hover:bg-gray-50 transition-all duration-200">
                        <div class="text-center">
                            <i data-lucide="plus" class="w-8 h-8 text-blue-600 group-hover:text-blue-800 mx-auto mb-2"></i>
                            <p class="text-sm font-medium text-blue-800  group-hover:text-blue-800">Create Exam</p>
                        </div>
                    </a>
                    <a href="{{ route('mcq.questions') }}" class="group p-4 border-2 border-dashed border-gray-300  rounded-xl hover:border-gray-500 hover:bg-gray-50 transition-all duration-200">
                        <div class="text-center">
                            <i data-lucide="help-circle" class="w-8 h-8 text-blue-600 group-hover:text-blue-800 mx-auto mb-2"></i>
                            <p class="text-sm font-medium text-blue-800  group-hover:text-blue-800">Add Question</p>
                        </div>
                    </a>
                    <a href="{{ route('mcq.students') }}" class="group p-4 border-2 border-dashed border-gray-300  rounded-xl hover:border-gray-500 hover:bg-gray-50 transition-all duration-200">
                        <div class="text-center">
                            <i data-lucide="users" class="w-8 h-8 text-blue-600 group-hover:text-blue-800 mx-auto mb-2"></i>
                            <p class="text-sm font-medium text-blue-800  group-hover:text-blue-800">Manage Users</p>
                        </div>
                    </a>
                    <a href="{{ route('mcq.analytics') }}" class="group p-4 border-2 border-dashed border-gray-300  rounded-xl hover:border-gray-500 hover:bg-gray-50 transition-all duration-200">
                        <div class="text-center">
                            <i data-lucide="bar-chart-3" class="w-8 h-8 text-blue-600 group-hover:text-blue-800 mx-auto mb-2"></i>
                            <p class="text-sm font-medium text-blue-800  group-hover:text-blue-800">View Reports</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white  rounded-2xl border border-gray-200 ">
            <div class="p-4 border-b border-gray-200 ">
                <h3 class="text-lg font-semibold text-blue-900 ">Recent Activity</h3>
            </div>
            <div class="p-4 space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-100 border-2 border-gray-300 rounded-xl flex items-center justify-center">
                        <i data-lucide="plus" class="w-4 h-4 text-blue-800"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-900 ">New exam created</p>
                        <p class="text-xs text-blue-700 ">Mathematics Quiz - 2 hours ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-100  rounded-xl flex items-center justify-center">
                        <i data-lucide="check" class="w-4 h-4 text-green-600 "></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-900 ">Exam completed</p>
                        <p class="text-xs text-blue-700 ">Physics Test by John Doe - 4 hours ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-100  rounded-xl flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-4 h-4 text-purple-600 "></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-900 ">New student registered</p>
                        <p class="text-xs text-blue-700 ">Jane Smith joined - 6 hours ago</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Student Available Exams -->
        <div class="bg-white  rounded-2xl border border-gray-200 ">
            <div class="p-4 border-b border-gray-200 ">
                <h3 class="text-lg font-semibold text-blue-900 ">Available Exams</h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="p-4 border border-gray-200  rounded-xl hover:border-gray-500 hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-blue-900 ">Mathematics Quiz</h4>
                        <span class="bg-green-100  text-green-800  px-2 py-1 rounded-lg text-xs font-medium">Active</span>
                    </div>
                    <p class="text-sm text-blue-800  mb-3">25 questions • 60 minutes</p>
                    <button class="w-full bg-gray-600 border-2 border-gray-600 hover:bg-gray-700 hover:border-gray-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center justify-center space-x-2">
                        <i data-lucide="play" class="w-4 h-4"></i>
                        <span>Start Exam</span>
                    </button>
                </div>
                <div class="p-4 border border-gray-200  rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-blue-900 ">Science Test</h4>
                        <span class="bg-yellow-100  text-yellow-800  px-2 py-1 rounded-lg text-xs font-medium">Scheduled</span>
                    </div>
                    <p class="text-sm text-blue-800  mb-3">40 questions • 90 minutes</p>
                    <button class="w-full bg-gray-300  text-gray-800  px-4 py-2 rounded-xl text-sm font-medium cursor-not-allowed flex items-center justify-center space-x-2">
                        <i data-lucide="clock" class="w-4 h-4 text-gray-800 "></i>
                        <span>Starts Tomorrow</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Leaderboard Preview -->
        <div class="bg-white  rounded-2xl border border-gray-200 ">
            <div class="p-4 border-b border-gray-200  flex items-center justify-between">
                <h3 class="text-lg font-semibold text-blue-900 ">Leaderboard</h3>
                <a href="{{ route('mcq.leaderboard') }}" class="text-sm text-primary-600  hover:text-primary-700  font-medium">View All →</a>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-yellow-50 to-yellow-100   rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-yellow-400 rounded-xl flex items-center justify-center">
                            <i data-lucide="crown" class="w-4 h-4 text-white"></i>
                        </div>
                        <span class="font-semibold text-blue-900 ">Jane Smith</span>
                    </div>
                    <span class="text-yellow-600  font-bold">95.8%</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50  rounded-xl">
                    <div class="flex items-center space-x-3">
                        <span class="w-8 h-8 bg-gray-400 rounded-xl flex items-center justify-center text-white font-bold text-sm">2</span>
                        <span class="font-medium text-blue-900 ">John Doe</span>
                    </div>
                    <span class="text-blue-800  font-bold">89.2%</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-orange-50  rounded-xl">
                    <div class="flex items-center space-x-3">
                        <span class="w-8 h-8 bg-orange-400 rounded-xl flex items-center justify-center text-white font-bold text-sm">3</span>
                        <span class="font-medium text-blue-900 ">Mike Johnson</span>
                    </div>
                    <span class="text-orange-600  font-bold">87.5%</span>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Add hover effects to stat cards
        const statCards = document.querySelectorAll('.group');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-2px)';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endpush
@endsection