@extends('layouts.mcq')

@section('title', 'Posts - MCQ System')
@section('page-title', $isAdmin ? 'Posts' : 'Announcements')
@section('page-subtitle', $isAdmin ? 'Manage announcements and news' : 'View announcements')

@section('content')
<div class="flex justify-between items-center mb-6">
  <div>
    <h3 class="text-xl font-semibold text-gray-900">{{ $isAdmin ? 'Post Management' : 'Announcements' }}</h3>
    <p class="text-gray-800">{{ $isAdmin ? 'Create and manage announcements' : 'Latest news and updates' }}</p>
  </div>
  @if($isAdmin)
  <button class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 inline" style="color: white; stroke: white;"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>Create Post
  </button>
  @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  @foreach($posts as $post)
  <!-- Post Card -->
  <div class="bg-white rounded-xl shadow-sm border-2 border-gray-300 p-6">
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full flex items-center justify-center
          bg-gray-100 border border-gray-300">
          <i class="fas fa-{{ $post['icon'] }}
            text-gray-800"></i>
        </div>
        <div>
          <h4 class="font-semibold text-gray-900">{{ $post['title'] }}</h4>
          <p class="text-sm text-gray-700">Posted {{ $post['posted_at'] }}</p>
        </div>
      </div>
      <span class="px-2 py-1 rounded-full text-xs
        bg-gray-100 border border-gray-300 text-gray-900">
        {{ $post['status'] }}
      </span>
    </div>
    <p class="text-gray-800 mb-4">{{ $post['content'] }}</p>
    <div class="flex items-center justify-between text-sm text-gray-700">
      <div class="flex items-center space-x-4">
        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1 inline" style="color: rgb(55, 65, 81); stroke: rgb(55, 65, 81);"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>{{ $post['views'] }} views</span>
        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1 inline" style="color: rgb(55, 65, 81); stroke: rgb(55, 65, 81);"><path d="m19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"></path></svg>{{ $post['likes'] }} likes</span>
      </div>
      <div class="flex space-x-2">
        {{-- Read Button for all users --}}
        <a href="{{ route('mcq.posts.show', $post['id']) }}" 
           class="px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-xs font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1 inline" style="color: white; stroke: white;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>Read
        </a>
        
        @if($isAdmin)
        <button class="text-gray-800 hover:text-gray-900 p-2 border border-gray-300 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" style="color: rgb(55, 65, 81); stroke: rgb(55, 65, 81);"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path><path d="m15 5 4 4"></path></svg></button>
        <button class="text-red-600 hover:text-red-800 p-2 border border-gray-300 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" style="color: rgb(220, 38, 38); stroke: rgb(220, 38, 38);"><path d="m3 6 3 0"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg></button>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
