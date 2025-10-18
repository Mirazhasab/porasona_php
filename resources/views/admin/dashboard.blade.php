@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="stats-grid grid grid-cols-2 lg:grid-cols-4">
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

</div>

<!-- Quick Actions or Recent Activity -->
<div class="main-content-grid grid grid-cols-1 lg:grid-cols-2">
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

  <div class="bg-white rounded-2xl border-2 border-gray-300 shadow-sm">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-800">Recent Users</h3>
    </div>
    <div class="p-6">
      <div class="space-y-4">
        @forelse($recentUsers as $recentUser)
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-gray-100 border border-gray-300 rounded-full flex items-center justify-center">
            <span class="text-xs font-medium text-gray-700">{{ substr($recentUser->name, 0, 1) }}</span>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-800">{{ $recentUser->name }}</p>
            <p class="text-xs text-gray-500">{{ $recentUser->email }}</p>
          </div>
          <span class="text-xs text-gray-500">{{ $recentUser->created_at->diffForHumans() }}</span>
        </div>
        @empty
        <p class="text-gray-500 text-center py-4">No recent users</p>
        @endforelse
      </div>
    </div>
  </div>

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
