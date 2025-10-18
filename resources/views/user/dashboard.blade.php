@extends('layouts.user')

@section('title', 'Student Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="stats-grid grid grid-cols-2 lg:grid-cols-4">
  @if($isAdmin)
  {{-- Admin Stats --}}
  <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-xs lg:text-sm font-medium text-gray-700">Total Questions</p>
        <p class="stat-number font-bold text-gray-800">{{ $stats['totalQuestions'] ?? 0 }}</p>
        <div class="stat-trend flex items-center">
          <i data-lucide="trending-up" class="w-3 h-3 text-green-500 mr-1"></i>
          <span class="text-xs text-gray-700">+12%</span>
        </div>
      </div>
      <div class="stat-icon bg-gray-100 border border-gray-300 flex items-center justify-center group-hover:scale-110 transition-transform">
        <i data-lucide="help-circle" class="text-gray-600"></i>
      </div>
    </div>
  </div>

  <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-xs lg:text-sm font-medium text-gray-700 ">Active Exams</p>
        <p class="text-xl lg:text-3xl font-bold text-gray-800  mt-1">{{ $stats['activeExams'] ?? 0 }}</p>
        <div class="flex items-center mt-2">
          <i data-lucide="trending-up" class="w-3 h-3 text-green-500 mr-1"></i>
          <span class="text-xs text-gray-700">+8%</span>
        </div>
      </div>
      <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
        <i data-lucide="clipboard-list" class="w-5 h-5 lg:w-6 lg:h-6 text-gray-700"></i>
      </div>
    </div>
  </div>

  <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-xs lg:text-sm font-medium text-gray-700 ">Total Students</p>
        <p class="text-xl lg:text-3xl font-bold text-gray-800  mt-1">{{ $stats['totalStudents'] ?? 0 }}</p>
        <div class="flex items-center mt-2">
          <i data-lucide="trending-down" class="w-3 h-3 text-red-500 mr-1"></i>
          <span class="text-xs text-gray-700">-3%</span>
        </div>
      </div>
      <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
        <i data-lucide="users" class="w-5 h-5 lg:w-6 lg:h-6 text-gray-700"></i>
      </div>
    </div>
  </div>

  <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-xs lg:text-sm font-medium text-gray-700 ">Avg. Score</p>
        <p class="text-xl lg:text-3xl font-bold text-gray-800  mt-1">{{ $stats['avgScore'] ?? 0 }}%</p>
        <div class="flex items-center mt-2">
          <i data-lucide="trending-up" class="w-3 h-3 text-green-500 mr-1"></i>
          <span class="text-xs text-gray-700">+5%</span>
        </div>
      </div>
      <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
        <i data-lucide="bar-chart-3" class="w-5 h-5 lg:w-6 lg:h-6 text-gray-700"></i>
      </div>
    </div>
  </div>
  @else
  {{-- Student Stats --}}
  <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-xs lg:text-sm font-medium text-gray-700 ">Exams Taken</p>
        <p class="text-xl lg:text-3xl font-bold text-gray-800  mt-1">{{ $stats['myExamsTaken'] ?? 0 }}</p>
        <div class="flex items-center mt-2">
          <i data-lucide="check" class="w-3 h-3 text-gray-700 mr-1"></i>
          <span class="text-xs text-gray-600">Keep it up!</span>
        </div>
      </div>
      <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 border-2 border-gray-300 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
        <i data-lucide="clipboard-check" class="w-5 h-5 lg:w-6 lg:h-6 text-gray-600"></i>
      </div>
    </div>
  </div>

  <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-xs lg:text-sm font-medium text-gray-700 ">My Average</p>
        <p class="text-xl lg:text-3xl font-bold text-gray-800  mt-1">{{ $stats['myAvgScore'] ?? 0 }}%</p>
        <div class="flex items-center mt-2">
          <i data-lucide="star" class="w-3 h-3 text-green-500 mr-1"></i>
          <span class="text-xs text-gray-700">Great job!</span>
        </div>
      </div>
      <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
        <i data-lucide="trophy" class="w-5 h-5 lg:w-6 lg:h-6 text-gray-700"></i>
      </div>
    </div>
  </div>

  <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-xs lg:text-sm font-medium text-gray-700 ">Active Exams</p>
        <p class="text-xl lg:text-3xl font-bold text-gray-800  mt-1">{{ $stats['activeExams'] ?? 0 }}</p>
        <div class="flex items-center mt-2">
          <i data-lucide="clock" class="w-3 h-3 text-purple-500 mr-1"></i>
          <span class="text-xs text-gray-700">Available now</span>
        </div>
      </div>
      <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
        <i data-lucide="clipboard-list" class="w-5 h-5 lg:w-6 lg:h-6 text-gray-700"></i>
      </div>
    </div>
  </div>

  <div class="stat-card bg-white hover:shadow-md transition-all duration-300 group">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-xs lg:text-sm font-medium text-gray-700 ">My Rank</p>
        <p class="text-xl lg:text-3xl font-bold text-gray-800  mt-1">
          @if($stats['userRank'])
            #{{ $stats['userRank'] }}
          @else
            --
          @endif
        </p>
        <div class="flex items-center mt-2">
          <i data-lucide="medal" class="w-3 h-3 text-yellow-500 mr-1"></i>
          <span class="text-xs text-gray-700">
            @if($stats['userRank'])
              Keep improving!
            @else
              Take an exam to get ranked
            @endif
          </span>
        </div>
      </div>
      <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 border border-gray-300 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
        <i data-lucide="medal" class="w-5 h-5 lg:w-6 lg:h-6 text-gray-700"></i>
      </div>
    </div>
  </div>
  @endif
</div>

<!-- Quick Actions or Recent Activity -->
<div class="main-content-grid grid grid-cols-1 lg:grid-cols-2">
  @if($isAdmin)
  {{-- Admin: Quick Actions --}}
  <div class="content-card bg-white">
    <div class="content-card-header">
      <h3 class="text-lg font-semibold text-gray-800">Quick Admin Actions</h3>
    </div>
    <div class="content-card-body">
      <div class="quick-actions-grid grid grid-cols-2">
        <a href="{{ route('mcq.examinations') }}" wire:navigate class="quick-action-btn border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50 transition-colors group block text-center">
          <i data-lucide="plus" class="text-blue-500 group-hover:text-blue-700 mx-auto"></i>
          <p class="text-sm font-medium text-blue-700 group-hover:text-blue-800">Create Exam</p>
        </a>
        <a href="{{ route('mcq.questions') }}" wire:navigate class="quick-action-btn border-dashed border-green-300 hover:border-green-500 hover:bg-green-50 transition-colors group block text-center">
          <i data-lucide="help-circle" class="text-green-500 group-hover:text-green-700 mx-auto"></i>
          <p class="text-sm font-medium text-green-700 group-hover:text-green-800">Add Question</p>
        </a>
        <a href="{{ route('mcq.students') }}" wire:navigate class="quick-action-btn border-dashed border-purple-300 hover:border-purple-500 hover:bg-purple-50 transition-colors group block text-center">
          <i data-lucide="users" class="text-purple-500 group-hover:text-purple-700 mx-auto"></i>
          <p class="text-sm font-medium text-purple-700 group-hover:text-purple-800">Manage Students</p>
        </a>
        <a href="{{ route('mcq.analytics') }}" wire:navigate class="quick-action-btn border-dashed border-orange-300 hover:border-orange-500 hover:bg-orange-50 transition-colors group block text-center">
          <i data-lucide="bar-chart-3" class="text-orange-500 group-hover:text-orange-700 mx-auto"></i>
          <p class="text-sm font-medium text-orange-700 group-hover:text-orange-800">View Reports</p>
        </a>
      </div>
    </div>
  </div>

  <div class="bg-white  rounded-2xl border-2 border-gray-300 shadow-sm">
    <div class="p-6 border-b border-gray-200 ">
      <h3 class="text-lg font-semibold text-gray-800 ">Recent Activity</h3>
    </div>
    <div class="p-6">
      <div class="space-y-4">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-gray-100 border-2 border-gray-300 rounded-full flex items-center justify-center">
            <i data-lucide="plus" class="w-4 h-4 text-gray-600"></i>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-800 ">New exam created</p>
            <p class="text-xs text-gray-500 ">Mathematics Quiz - 2 hours ago</p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-gray-100 border border-gray-300 rounded-full flex items-center justify-center">
            <i data-lucide="check" class="w-4 h-4 text-gray-700"></i>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-800 ">Exam completed</p>
            <p class="text-xs text-gray-500 ">Physics Test by John Doe - 4 hours ago</p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-gray-100 border border-gray-300 rounded-full flex items-center justify-center">
            <i data-lucide="user-plus" class="w-4 h-4 text-gray-700"></i>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-800 ">New student registered</p>
            <p class="text-xs text-gray-500 ">Jane Smith joined - 6 hours ago</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  @else
  {{-- Student: Available Exams and Leaderboard Preview --}}
  <div class="bg-white  rounded-2xl border-2 border-gray-300 shadow-sm">
    <div class="p-6 border-b border-gray-200 ">
      <h3 class="text-lg font-semibold text-gray-800 ">Available Exams</h3>
    </div>
    <div class="p-6">
      <div class="space-y-4">
        @forelse($availableExams as $exam)
        <div class="dashboard-exam-card block p-4 border-2 border-gray-300 rounded-lg hover:border-gray-500 hover:bg-gray-50 transition-colors">
          <div class="flex items-center justify-between mb-2">
            <h4 class="font-semibold text-gray-800 ">{{ $exam->title ?? $exam->exam_name }}</h4>
            <span class="bg-gray-100 border border-gray-300 text-gray-700 px-2 py-1 rounded-full text-xs">
              {{ ucfirst($exam->status) }}
            </span>
          </div>
          <p class="text-sm text-gray-700  mb-3">
            {{ $exam->questions_count }} questions • {{ $exam->duration ?? 60 }} minutes
          </p>
          <a href="{{ route('mcq.examinations') }}" wire:navigate class="exam-card-button bg-blue-600 border-2 border-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 hover:border-blue-700 transition-colors w-full flex items-center justify-center font-medium">
            <i data-lucide="play" class="w-4 h-4 mr-2 text-white"></i>Start Exam
          </a>
        </div>
        @empty
        <div class="text-center py-8">
          <i data-lucide="clipboard-x" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
          <p class="text-gray-500 ">No exams available at the moment</p>
          <p class="text-sm text-gray-400 ">Check back later for new exams</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="bg-white  rounded-2xl border-2 border-gray-300 shadow-sm">
    <div class="p-6 border-b border-gray-200  flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-800 ">Leaderboard</h3>
      <a href="{{ route('mcq.leaderboard') }}" wire:navigate class="text-sm text-gray-700 hover:text-gray-800">View All →</a>
    </div>
    <div class="p-6">
      <div class="space-y-3">
        @forelse($leaderboard as $index => $leader)
        <div class="flex items-center justify-between p-3 bg-white border border-gray-300 rounded-lg">
          <div class="flex items-center space-x-3">
            @if($index === 0)
              <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                <i data-lucide="crown" class="w-4 h-4 text-white"></i>
              </div>
            @else
              <span class="w-8 h-8 
                @if($index === 1) bg-gray-400
                @elseif($index === 2) bg-orange-400
                @else bg-gray-300
                @endif rounded-full flex items-center justify-center text-white font-bold text-sm">
                {{ $index + 1 }}
              </span>
            @endif
            <span class="font-medium text-gray-800 ">{{ $leader->name }}</span>
          </div>
          <span class="
            text-gray-700 font-bold">
            {{ number_format($leader->avg_score, 1) }}%
          </span>
        </div>
        @empty
        <div class="text-center py-8">
          <i data-lucide="users" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
          <p class="text-gray-500 ">No rankings available yet</p>
          <p class="text-sm text-gray-400 ">Complete exams to see the leaderboard</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>
  @endif
</div>

<!-- Recent Posts Section -->
<div class="mt-6">
  <div class="bg-white  rounded-2xl border-2 border-gray-300 shadow-sm">
    <div class="p-6 border-b border-gray-200  flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-800 ">Recent Posts</h3>
      <a href="{{ route('mcq.posts.index') }}" wire:navigate class="text-sm text-gray-700 hover:text-gray-800">View All →</a>
    </div>
    <div class="p-6">
      <div class="space-y-4">
        @forelse($recentPosts as $post)
        <a href="{{ route('mcq.posts.show', $post) }}" wire:navigate class="block p-4 border-2 border-gray-300 rounded-lg hover:border-gray-500 hover:bg-gray-50 transition-colors">
          <div class="flex items-start justify-between mb-2">
            <div class="flex-1">
              <h4 class="font-semibold text-gray-800  mb-1">{{ $post->title }}</h4>
              <p class="text-sm text-gray-700  line-clamp-2">
                {{ Str::limit(strip_tags($post->content), 100) }}
              </p>
            </div>
            @if($post->category)
            <span class="ml-3 px-2 py-1 rounded-full text-xs font-medium" 
                  style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }};">
              {{ $post->category->name }}
            </span>
            @endif
          </div>
          <div class="flex items-center justify-between text-xs text-gray-500 ">
            <div class="flex items-center space-x-3">
              <span class="flex items-center">
                <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                {{ $post->user->name }}
              </span>
              <span class="flex items-center">
                <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                {{ $post->created_at->diffForHumans() }}
              </span>
            </div>
            <div class="flex items-center space-x-2">
              @if($post->views > 0)
              <span class="flex items-center">
                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                {{ $post->views }}
              </span>
              @endif
              @if($post->likes > 0)
              <span class="flex items-center">
                <i data-lucide="heart" class="w-3 h-3 mr-1"></i>
                {{ $post->likes }}
              </span>
              @endif
            </div>
          </div>
        </a>
        @empty
        <div class="text-center py-8">
          <i data-lucide="newspaper" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
          <p class="text-gray-500 ">No recent posts available</p>
          <p class="text-sm text-gray-400 ">Check back later for updates</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
