<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
                <tr id="post-{{ $post->id }}">
                    <td>
                        <strong>{{ $post->title }}</strong>
                        @if($post->featured_image)
                            <br><small class="text-muted">📷 Has Image</small>
                        @endif
                        @if($post->tags->count() > 0)
                            <br>
                            @foreach($post->tags as $tag)
                                <span class="badge badge-secondary">{{ $tag->name }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>{{ $post->user->name }}</td>
                    <td>{{ $post->category->name }}</td>
                    <td>
                        @switch($post->status)
                            @case('pending')
                                <span class="badge badge-warning">Pending</span>
                                @break
                            @case('approved')
                                <span class="badge badge-info">Approved</span>
                                @break
                            @case('published')
                                <span class="badge badge-success">Published</span>
                                @break
                            @case('archived')
                                <span class="badge" style="background-color: #fff; color: #222; border: 1px solid #222;">Archived</span>
                                @break
                            @default
                                <span class="badge badge-secondary">{{ ucfirst($post->status) }}</span>
                        @endswitch
                    </td>
                    <td>{{ $post->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            @can('update', $post)
                                <button class="btn btn-outline-primary edit-post" data-id="{{ $post->id }}">
                                    Edit
                                </button>
                            @endcan
                            
                            @can('approve', $post)
                                @if($post->status === 'pending')
                                    <button class="btn btn-outline-success approve-post" data-id="{{ $post->id }}">
                                        Approve
                                    </button>
                                @endif
                                
                                @if($post->status === 'approved')
                                    <button class="btn btn-outline-info publish-post" data-id="{{ $post->id }}">
                                        Publish
                                    </button>
                                @endif
                                
                                @if(in_array($post->status, ['approved', 'published']))
                                    <button class="btn btn-outline-warning archive-post" data-id="{{ $post->id }}">
                                        Archive
                                    </button>
                                @endif
                            @endcan
                            
                            @can('delete', $post)
                                <button class="btn btn-outline-danger delete-post" data-id="{{ $post->id }}">
                                    Delete
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        No posts found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>