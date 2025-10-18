@extends('layouts.mcq')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Create New Post</h5>
                    <a href="/mcq/posts" class="btn btn-secondary">Back to Posts</a>
                </div>
                <div class="card-body">
                    <form id="createPostForm" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="content">Content *</label>
                                    <div id="editor" style="height: 400px;"></div>
                                    <textarea name="content" id="content" style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category_id">Category *</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="featured_image">Featured Image</label>
                                    <input type="file" class="form-control-file" id="featured_image" name="featured_image" accept="image/*">
                                    <div id="image-preview" class="mt-2" style="display: none;">
                                        <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tags">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags_input" placeholder="Enter tags separated by commas">
                                    <small class="form-text text-muted">Example: technology, web development, laravel</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="alert alert-info">
                                        <small>Your post will be saved as <strong>Pending</strong> and require admin approval before publishing.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                                Create Post
                            </button>
                            <a href="/mcq/posts" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Quill editor
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['blockquote', 'code-block'],
                ['link', 'image'],
                ['clean']
            ]
        }
    });
    
    // Image preview
    $('#featured_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').hide();
        }
    });
    
    // Form submission
    $('#createPostForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get content from Quill editor
        const content = quill.root.innerHTML;
        $('#content').val(content);
        
        // Validate content
        if (quill.getText().trim().length === 0) {
            showAlert('error', 'Content is required.');
            return;
        }
        
        // Show loading state
        $('#submitBtn').prop('disabled', true);
        $('#spinner').removeClass('d-none');
        
        // Prepare form data
        const formData = new FormData(this);
        
        // Process tags
        const tagsInput = $('#tags').val();
        if (tagsInput) {
            const tags = tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag);
            tags.forEach(tag => {
                formData.append('tags[]', tag);
            });
        }
        
        // Submit form
        $.ajax({
            url: '/mcq/posts',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
                
                // Reset form
                $('#createPostForm')[0].reset();
                quill.setContents([]);
                $('#image-preview').hide();
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '/mcq/posts';
                }, 2000);
            }
        })
        .fail(function(xhr) {
            let message = 'An error occurred. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                message = Object.values(errors).flat().join('<br>');
            }
            
            showAlert('error', message);
        })
        .always(function() {
            $('#submitBtn').prop('disabled', false);
            $('#spinner').addClass('d-none');
        });
    });
    
    // Utility function for alerts
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = `<div class="alert ${alertClass} alert-dismissible fade show">
            ${message}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>`;
        
        $('.card-body').prepend(alert);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
        
        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 300);
    }
});
</script>
@endpush