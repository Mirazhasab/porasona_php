@extends('layouts.mcq')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5>Post Management</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/mcq/posts" class="list-group-item list-group-item-action {{ !request('status') ? 'active' : '' }}">
                        All Posts <span class="badge badge-secondary float-right" id="all-count">0</span>
                    </a>
                    <a href="/mcq/posts?status=pending" class="list-group-item list-group-item-action {{ request('status') == 'pending' ? 'active' : '' }}">
                        Pending <span class="badge badge-warning float-right" id="pending-count">0</span>
                    </a>
                    <a href="/mcq/posts?status=approved" class="list-group-item list-group-item-action {{ request('status') == 'approved' ? 'active' : '' }}">
                        Approved <span class="badge badge-info float-right" id="approved-count">0</span>
                    </a>
                    <a href="/mcq/posts?status=published" class="list-group-item list-group-item-action {{ request('status') == 'published' ? 'active' : '' }}">
                        Published <span class="badge badge-success float-right" id="published-count">0</span>
                    </a>
                    <a href="/mcq/posts?status=archived" class="list-group-item list-group-item-action {{ request('status') == 'archived' ? 'active' : '' }}">
                        Archived <span class="badge float-right" id="archived-count" style="background-color: #fff; color: #222; border: 1px solid #222;">0</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Posts</h5>
                    <a href="/mcq/posts/create" class="btn btn-primary">Create New Post</a>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="search" placeholder="Search posts..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="category-filter">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" id="clear-filters">Clear</button>
                        </div>
                    </div>

                    <!-- Posts List -->
                    <div id="posts-container">
                        @include('posts.partials.post-list', ['posts' => $posts])
                    </div>

                    <!-- Pagination -->
                    <div id="pagination-container">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Post Modal -->
<div class="modal fade" id="editPostModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editPostForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Post</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea class="form-control" name="content" rows="10" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Featured Image</label>
                        <input type="file" class="form-control-file" name="featured_image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Tags (comma separated)</label>
                        <input type="text" class="form-control" name="tags_input" placeholder="tag1, tag2, tag3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    
    // Search functionality
    $('#search').on('keyup', debounce(function() {
        loadPosts();
    }, 500));
    
    // Category filter
    $('#category-filter').on('change', function() {
        loadPosts();
    });
    
    // Clear filters
    $('#clear-filters').on('click', function() {
        $('#search').val('');
        $('#category-filter').val('');
        loadPosts();
    });
    
    // Load posts via AJAX
    function loadPosts(page = 1) {
        const params = {
            search: $('#search').val(),
            category: $('#category-filter').val(),
            status: '{{ request("status") }}',
            page: page
        };
        
        $.get('/mcq/posts', params)
            .done(function(response) {
                $('#posts-container').html(response.html);
                $('#pagination-container').html(response.pagination);
                updateCounts();
            });
    }
    
    // Post actions
    $(document).on('click', '.approve-post', function() {
        const postId = $(this).data('id');
        postAction(postId, 'approve');
    });
    
    $(document).on('click', '.publish-post', function() {
        const postId = $(this).data('id');
        postAction(postId, 'publish');
    });
    
    $(document).on('click', '.archive-post', function() {
        const postId = $(this).data('id');
        postAction(postId, 'archive');
    });
    
    $(document).on('click', '.delete-post', function() {
        const postId = $(this).data('id');
        if (confirm('Are you sure you want to delete this post?')) {
            deletePost(postId);
        }
    });
    
    // Edit post
    $(document).on('click', '.edit-post', function() {
        const postId = $(this).data('id');
        editPost(postId);
    });
    
    // Post actions
    function postAction(postId, action) {
        $.post(`/mcq/posts/${postId}/${action}`, {
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadPosts(currentPage);
            }
        })
        .fail(function() {
            showAlert('error', 'Action failed. Please try again.');
        });
    }
    
    // Delete post
    function deletePost(postId) {
        $.ajax({
            url: `/mcq/posts/${postId}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' }
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadPosts(currentPage);
            }
        })
        .fail(function() {
            showAlert('error', 'Delete failed. Please try again.');
        });
    }
    
    // Edit post
    function editPost(postId) {
        $.get(`/mcq/posts/${postId}/edit`)
            .done(function(response) {
                const post = response.post;
                const form = $('#editPostForm');
                
                form.attr('action', `/mcq/posts/${postId}`);
                form.find('[name="title"]').val(post.title);
                form.find('[name="category_id"]').val(post.category_id);
                form.find('[name="content"]').val(post.content);
                
                const tags = post.tags.map(tag => tag.name).join(', ');
                form.find('[name="tags_input"]').val(tags);
                
                $('#editPostModal').modal('show');
            });
    }
    
    // Submit edit form
    $('#editPostForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = $(this).attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false
        })
        .done(function(response) {
            if (response.success) {
                $('#editPostModal').modal('hide');
                showAlert('success', response.message);
                loadPosts(currentPage);
            }
        })
        .fail(function() {
            showAlert('error', 'Update failed. Please try again.');
        });
    });
    
    // Pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');
        currentPage = page;
        loadPosts(page);
    });
    
    // Update counts
    function updateCounts() {
        // This would be updated via separate AJAX call in real implementation
    }
    
    // Utility functions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = `<div class="alert ${alertClass} alert-dismissible fade show">
            ${message}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>`;
        
        $('.card-body').prepend(alert);
        setTimeout(() => $('.alert').fadeOut(), 3000);
    }
});
</script>
@endpush