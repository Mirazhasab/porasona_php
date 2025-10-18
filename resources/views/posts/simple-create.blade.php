@extends('layouts.mcq')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Create New Post</h2>
                    <a href="/mcq/posts" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Back to Posts
                    </a>
                </div>

                <form id="createPostForm" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input type="text" name="title" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                        <textarea name="content" required rows="10"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Write your post content here..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                        <input type="file" name="featured_image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <input type="text" name="tags_input" 
                               placeholder="Enter tags separated by commas (e.g., technology, web, laravel)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Your post will be saved as <strong>Pending</strong> and require admin approval before publishing.
                        </p>
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" id="submitBtn"
                                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 disabled:opacity-50">
                            <span id="spinner" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                            </span>
                            Create Post
                        </button>
                        <a href="/mcq/posts" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('createPostForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    
    // Show loading state
    submitBtn.disabled = true;
    spinner.classList.remove('hidden');
    
    // Prepare form data
    const formData = new FormData(this);
    
    // Process tags
    const tagsInput = formData.get('tags_input');
    if (tagsInput) {
        const tags = tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag);
        formData.delete('tags_input');
        tags.forEach(tag => {
            formData.append('tags[]', tag);
        });
    }
    
    // Submit form
    fetch('/mcq/posts', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            
            // Reset form
            this.reset();
            
            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = '/mcq/posts';
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to create post');
        }
    })
    .catch(error => {
        showAlert('error', error.message || 'An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        spinner.classList.add('hidden');
    });
});

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
    const alert = document.createElement('div');
    alert.className = `${alertClass} border px-4 py-3 rounded mb-4`;
    alert.innerHTML = `
        <span class="block sm:inline">${message}</span>
        <button type="button" class="float-right" onclick="this.parentElement.remove()">×</button>
    `;
    
    const form = document.getElementById('createPostForm');
    form.parentNode.insertBefore(alert, form);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endsection