@extends('layouts.mcq')

@section('title', 'Posts - MCQ Pro')
@section('page-title', 'Post Management')
@section('page-subtitle', 'Create and manage posts')

@section('content')
<div class="space-y-6">
    <!-- Mobile Filter Tabs -->
    <div class="lg:hidden">
        <div class="flex space-x-1 bg-gray-100  p-1 rounded-xl">
            <a href="/mcq/posts" class="flex-1 text-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ !request('status') ? 'bg-white  text-primary-600  shadow-sm' : 'text-blue-800 ' }}">All</a>
            <a href="/mcq/posts?status=pending" class="flex-1 text-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request('status') == 'pending' ? 'bg-white  text-primary-600  shadow-sm' : 'text-blue-800 ' }}">Pending</a>
            <a href="/mcq/posts?status=published" class="flex-1 text-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request('status') == 'published' ? 'bg-white  text-primary-600  shadow-sm' : 'text-blue-800 ' }}">Published</a>
        </div>
    </div>

    <div class="bg-white  rounded-2xl border border-gray-200 ">
        <div class="p-4 lg:p-6 border-b border-gray-200 ">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                <div>
                    <h2 class="text-xl lg:text-2xl font-bold text-blue-900 ">Posts</h2>
                    <p class="text-sm text-blue-800  mt-1">Manage your content</p>
                </div>
                <a href="/mcq/posts/create" class="inline-flex items-center justify-center space-x-2 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Create Post</span>
                </a>
            </div>
        </div>

        <!-- Desktop Filter Tabs -->
        <div class="hidden lg:block px-6 py-4 border-b border-gray-200 ">
            <div class="flex space-x-6">
                <a href="/mcq/posts" class="pb-2 text-sm font-medium transition-colors {{ !request('status') ? 'border-b-2 border-primary-500 text-primary-600 ' : 'text-blue-800  hover:text-blue-900 ' }}">
                    All Posts ({{ $posts->total() }})
                </a>
                <a href="/mcq/posts?status=pending" class="pb-2 text-sm font-medium transition-colors {{ request('status') == 'pending' ? 'border-b-2 border-primary-500 text-primary-600 ' : 'text-blue-800  hover:text-blue-900 ' }}">
                    Pending
                </a>
                <a href="/mcq/posts?status=approved" class="pb-2 text-sm font-medium transition-colors {{ request('status') == 'approved' ? 'border-b-2 border-primary-500 text-primary-600 ' : 'text-blue-800  hover:text-blue-900 ' }}">
                    Approved
                </a>
                <a href="/mcq/posts?status=published" class="pb-2 text-sm font-medium transition-colors {{ request('status') == 'published' ? 'border-b-2 border-primary-500 text-primary-600 ' : 'text-blue-800  hover:text-blue-900 ' }}">
                    Published
                </a>
            </div>
        </div>

        <!-- Posts List -->
        <div class="p-4 lg:p-6">
            <div class="space-y-3 lg:space-y-4">
                @forelse($posts as $post)
                    <div class="border border-gray-200  rounded-xl p-4 hover:shadow-lg hover:border-primary-200  transition-all duration-200 group">
                        <!-- Mobile Layout -->
                        <div class="lg:hidden">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold text-blue-900  truncate">{{ $post->title }}</h3>
                                    <p class="text-xs text-blue-700  mt-1">
                                        {{ $post->user->name }} • {{ $post->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @switch($post->status)
                                    @case('pending')
                                        <span class="bg-yellow-100  text-yellow-800  px-2 py-1 rounded-lg text-xs font-medium">Pending</span>
                                        @break
                                    @case('approved')
                                        <span class="bg-blue-100  text-blue-800  px-2 py-1 rounded-lg text-xs font-medium">Approved</span>
                                        @break
                                    @case('published')
                                        <span class="bg-green-100  text-green-800  px-2 py-1 rounded-lg text-xs font-medium">Published</span>
                                        @break
                                    @default
                                        <span class="bg-gray-100  text-blue-900  px-2 py-1 rounded-lg text-xs font-medium">{{ ucfirst($post->status) }}</span>
                                @endswitch
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-blue-700 ">{{ $post->category->name }}</span>
                                <div class="flex space-x-2">
                                    @php
                                        $user = auth()->user();
                                        $isAdmin = $user->role === 'admin';
                                        $canEdit = $isAdmin || ($user->id === $post->user_id && $post->status === 'pending');
                                        $canRead = $post->status === 'published' || $isAdmin || $user->id === $post->user_id;
                                    @endphp
                                    
                                    @if($canRead)
                                        <a href="{{ route('mcq.posts.show', $post) }}" class="p-1 text-primary hover:bg-primary/10 rounded">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                    
                                    @if($canEdit)
                                        <button onclick="editPost({{ $post->id }})" class="p-1 text-blue-600  hover:bg-blue-50  rounded">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    
                                    @if($isAdmin && $post->status === 'pending')
                                        <button onclick="approvePost({{ $post->id }})" class="p-1 text-green-600  hover:bg-green-50  rounded">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    
                                    @if($isAdmin && $post->status === 'approved')
                                        <button onclick="publishPost({{ $post->id }})" class="p-1 text-purple-600  hover:bg-purple-50  rounded">
                                            <i data-lucide="send" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    
                                    @if($isAdmin)
                                        <button onclick="deletePost({{ $post->id }})" class="p-1 text-red-600  hover:bg-red-50  rounded">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Desktop Layout -->
                        <div class="hidden lg:flex lg:items-center lg:justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-blue-900 ">{{ $post->title }}</h3>
                                <p class="text-sm text-blue-800  mt-1">
                                    By {{ $post->user->name }} • {{ $post->category->name }} • {{ $post->created_at->diffForHumans() }}
                                </p>
                                <div class="mt-2">
                                    @switch($post->status)
                                        @case('pending')
                                            <span class="bg-yellow-100  text-yellow-800  px-2 py-1 rounded-lg text-xs font-medium">Pending</span>
                                            @break
                                        @case('approved')
                                            <span class="bg-blue-100  text-blue-800  px-2 py-1 rounded-lg text-xs font-medium">Approved</span>
                                            @break
                                        @case('published')
                                            <span class="bg-green-100  text-green-800  px-2 py-1 rounded-lg text-xs font-medium">Published</span>
                                            @break
                                        @default
                                            <span class="bg-gray-100  text-blue-900  px-2 py-1 rounded-lg text-xs font-medium">{{ ucfirst($post->status) }}</span>
                                    @endswitch
                                </div>
                            </div>
                                
                                <div class="flex space-x-2 ml-4">
                                    @php
                                        $user = auth()->user();
                                        $isAdmin = $user->role === 'admin';
                                        $canEdit = $isAdmin || ($user->id === $post->user_id && $post->status === 'pending');
                                        $canRead = $post->status === 'published' || $isAdmin || $user->id === $post->user_id;
                                    @endphp
                                    
                                    @if($canRead)
                                        <a href="{{ route('mcq.posts.show', $post) }}" class="text-primary hover:text-primary/80 text-sm font-medium">
                                            <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>Read
                                        </a>
                                    @endif
                                    
                                    @if($canEdit)
                                        <button onclick="editPost({{ $post->id }})" class="text-blue-600 hover:text-blue-800 text-sm">
                                            Edit
                                        </button>
                                    @endif
                                    
                                    @if($isAdmin)
                                        @if($post->status === 'pending')
                                            <button onclick="approvePost({{ $post->id }})" class="text-green-600 hover:text-green-800 text-sm">
                                                Approve
                                            </button>
                                        @endif
                                        
                                        @if($post->status === 'approved')
                                            <button onclick="publishPost({{ $post->id }})" class="text-purple-600 hover:text-purple-800 text-sm">
                                                Publish
                                            </button>
                                        @endif
                                        
                                        <button onclick="deletePost({{ $post->id }})" class="text-red-600 hover:text-red-800 text-sm">
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-blue-700">
                            <i class="fas fa-newspaper text-4xl mb-4"></i>
                            <p>No posts found.</p>
                            <a href="/mcq/posts/create" class="text-blue-600 hover:text-blue-800">Create your first post</a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="mt-6">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});

function approvePost(id) {
    if (confirm('Approve this post?')) {
        const token = document.querySelector('meta[name="csrf-token"]');
        console.log('CSRF token found:', !!token);
        
        fetch(`/mcq/posts/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token ? token.content : '',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Failed to approve post', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Network error: ' + error.message);
        });
    }
}

function publishPost(id) {
    if (confirm('Publish this post?')) {
        fetch(`/mcq/posts/${id}/publish`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('error', data.message || 'Failed to publish post');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Network error: ' + error.message);
        });
    }
}

function deletePost(id) {
    if (confirm('Are you sure you want to delete this post?')) {
        fetch(`/mcq/posts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('error', data.message || 'Failed to delete post');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Network error: ' + error.message);
        });
    }
}

function editPost(id) {
    // For now, just show an alert - you can implement modal editing later
    alert('Edit functionality will be implemented. Post ID: ' + id);
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
    const alert = document.createElement('div');
    alert.className = `${alertClass} border px-4 py-3 rounded mb-4 fixed top-4 right-4 z-50 max-w-md`;
    alert.innerHTML = `
        <span class="block sm:inline">${message}</span>
        <button type="button" class="float-right ml-4" onclick="this.parentElement.remove()">×</button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 3000);
}
</script>
@endpush
@endsection