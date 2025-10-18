@extends('layouts.mcq')

@section('title', $post->title . ' - MCQ System')
@section('page-title', 'Read Post')
@section('page-subtitle', 'Full article content')

@section('content')
<div class="max-w-4xl mx-auto">
  {{-- Back Button --}}
  <div class="mb-6">
    <a href="{{ route('mcq.posts.index') }}" class="text-primary hover:text-secondary flex items-center">
      <i class="fas fa-arrow-left mr-2"></i>Back to Posts
    </a>
  </div>

  {{-- Main Post Content --}}
  <article class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    {{-- Featured Image --}}
    @if($post->featured_image)
    <div class="w-full h-64 md:h-80 bg-gray-200 overflow-hidden">
      <img src="{{ Storage::url($post->featured_image) }}" 
           alt="{{ $post->title }}" 
           class="w-full h-full object-cover">
    </div>
    @endif

    {{-- Post Header --}}
    <div class="p-8">
      {{-- Category and Status --}}
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
          @if($post->category)
          <span class="px-3 py-1 rounded-full text-sm font-semibold"
                style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }};">
            <i class="fas fa-folder mr-1"></i>{{ $post->category->name }}
          </span>
          @endif
          
          <span class="px-3 py-1 rounded-full text-xs
            @if($post->status === 'published') bg-green-100 text-green-800
            @elseif($post->status === 'pending') bg-yellow-100 text-yellow-800
            @elseif($post->status === 'draft') bg-gray-100 text-gray-800
            @else bg-blue-100 text-blue-800
            @endif">
            {{ ucfirst($post->status) }}
          </span>
        </div>

        {{-- Post Stats --}}
        <div class="flex items-center space-x-4 text-sm text-gray-500">
          <span><i class="fas fa-eye mr-1"></i>{{ $post->views }} views</span>
          <span><i class="fas fa-heart mr-1"></i>{{ $post->likes }} likes</span>
        </div>
      </div>

      {{-- Post Title --}}
      <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 leading-tight">
        {{ $post->title }}
      </h1>

      {{-- Author and Date Info --}}
      <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
          <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center">
            <span class="text-white font-bold text-lg">
              {{ strtoupper(substr($post->user->name, 0, 1)) }}
            </span>
          </div>
          <div>
            <p class="font-semibold text-gray-800">{{ $post->user->name }}</p>
            <p class="text-sm text-gray-500">
              @if($post->published_at)
                Published {{ $post->published_at->format('M d, Y \a\t h:i A') }}
              @else
                Created {{ $post->created_at->format('M d, Y \a\t h:i A') }}
              @endif
            </p>
          </div>
        </div>

        {{-- Action Buttons for Admin/Author --}}
        @if(auth()->user()->role === 'admin' || auth()->user()->id === $post->user_id)
        <div class="flex space-x-2">
          <a href="{{ route('mcq.posts.edit', $post) }}" 
             class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-1"></i>Edit
          </a>
          @if(auth()->user()->role === 'admin' && $post->status !== 'published')
          <button onclick="publishPost({{ $post->id }})"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            <i class="fas fa-check mr-1"></i>Publish
          </button>
          @endif
        </div>
        @endif
      </div>

      {{-- Post Content --}}
      <div class="prose prose-lg max-w-none">
        {!! nl2br(e($post->content)) !!}
      </div>

      {{-- Tags --}}
      @if($post->tags && $post->tags->count() > 0)
      <div class="mt-8 pt-6 border-t border-gray-200">
        <h4 class="text-sm font-semibold text-gray-600 mb-3">Tags:</h4>
        <div class="flex flex-wrap gap-2">
          @foreach($post->tags as $tag)
          <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
            <i class="fas fa-tag mr-1"></i>{{ $tag->name }}
          </span>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Engagement Actions --}}
      <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="flex space-x-4">
            <button onclick="likePost({{ $post->id }})" 
                    class="flex items-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
              <i class="fas fa-heart"></i>
              <span>Like ({{ $post->likes }})</span>
            </button>
            <button onclick="sharePost()" 
                    class="flex items-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
              <i class="fas fa-share"></i>
              <span>Share</span>
            </button>
          </div>
          
          <div class="text-sm text-gray-500">
            Last updated: {{ $post->updated_at->format('M d, Y') }}
          </div>
        </div>
      </div>
    </div>
  </article>

  {{-- Related Posts --}}
  @if($relatedPosts && $relatedPosts->count() > 0)
  <div class="mt-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Related Posts</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      @foreach($relatedPosts as $relatedPost)
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center space-x-2 mb-3">
          @if($relatedPost->category)
          <span class="px-2 py-1 rounded-full text-xs font-semibold"
                style="background-color: {{ $relatedPost->category->color }}20; color: {{ $relatedPost->category->color }};">
            {{ $relatedPost->category->name }}
          </span>
          @endif
        </div>
        
        <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
          {{ $relatedPost->title }}
        </h3>
        
        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
          {{ Str::limit(strip_tags($relatedPost->content), 120) }}
        </p>
        
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2 text-xs text-gray-500">
            <span><i class="fas fa-eye mr-1"></i>{{ $relatedPost->views }}</span>
            <span><i class="fas fa-heart mr-1"></i>{{ $relatedPost->likes }}</span>
          </div>
          
          <a href="{{ route('mcq.posts.show', $relatedPost) }}" 
             class="text-primary hover:text-secondary font-semibold text-sm">
            Read More <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif
</div>

{{-- JavaScript for interactions --}}
<script>
function likePost(postId) {
    // Add like functionality here
    fetch(`/mcq/posts/${postId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update like count
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function sharePost() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $post->title }}',
            text: '{{ Str::limit(strip_tags($post->content), 100) }}',
            url: window.location.href,
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}

function publishPost(postId) {
    if (confirm('Are you sure you want to publish this post?')) {
        fetch(`/mcq/posts/${postId}/publish`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while publishing the post.');
        });
    }
}
</script>

<style>
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

.prose {
    color: #374151;
    line-height: 1.75;
}

.prose p {
    margin-bottom: 1.25rem;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #111827;
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose ul, .prose ol {
    margin-bottom: 1.25rem;
    padding-left: 1.5rem;
}

.prose li {
    margin-bottom: 0.5rem;
}
</style>
@endsection
