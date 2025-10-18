<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category', 'tags']);
        
        if ($request->status) {
            $query->byStatus($request->status);
        }
        
        if ($request->search) {
            $query->search($request->search);
        }
        
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Admin can see all posts, students can see their own posts + published posts
        if (auth()->user()->role !== 'admin') {
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('status', 'published');
            });
        }
        
        $posts = $query->latest()->paginate(10);
        $categories = Category::all();
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('posts.partials.post-list', compact('posts'))->render(),
                'pagination' => $posts->links()->render()
            ]);
        }
        
        return view('posts.simple-index', compact('posts', 'categories'));
    }

    public function show(Post $post)
    {
        $user = auth()->user();
        
        // Check if user can view this post
        if ($post->status !== 'published' && $user->role !== 'admin' && $user->id !== $post->user_id) {
            abort(403, 'You do not have permission to view this post.');
        }
        
        // Increment view count
        $post->increment('views');
        
        // Load relationships
        $post->load(['user', 'category', 'tags']);
        
        // Get related posts (same category, published, excluding current post)
        $relatedPosts = Post::with(['user', 'category'])
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->latest()
            ->limit(3)
            ->get();
        
        return view('posts.show', compact('post', 'relatedPosts'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('posts.simple-create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'array'
        ]);

        $data = $request->only(['title', 'content', 'category_id']);
        $data['user_id'] = auth()->id();
        // All posts start as pending and require admin approval
        $data['status'] = 'pending';

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post = Post::create($data);

        if ($request->tags) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully and is pending approval.',
            'post' => $post->load(['category', 'tags'])
        ]);
    }

    public function edit(Post $post)
    {
        $user = auth()->user();
        // Students can only edit their own pending posts, admins can edit any post
        if ($user->role !== 'admin' && ($user->id !== $post->user_id || !in_array($post->status, ['pending', 'draft']))) {
            return response()->json(['success' => false, 'message' => 'You can only edit your own pending or draft posts'], 403);
        }
        
        $categories = Category::all();
        $tags = Tag::all();
        
        return response()->json([
            'post' => $post->load(['category', 'tags']),
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $user = auth()->user();
        // Students can only update their own pending posts, admins can update any post
        if ($user->role !== 'admin' && ($user->id !== $post->user_id || !in_array($post->status, ['pending', 'draft']))) {
            return response()->json(['success' => false, 'message' => 'You can only update your own pending or draft posts'], 403);
        }
        
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['title', 'content', 'category_id']);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update($data);

        if ($request->tags) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully.',
            'post' => $post->load(['category', 'tags'])
        ]);
    }

    public function destroy(Post $post)
    {
        $user = auth()->user();
        // Students can delete their own posts (any status), admins can delete any post
        if ($user->role !== 'admin' && $user->id !== $post->user_id) {
            return response()->json(['success' => false, 'message' => 'You can only delete your own posts'], 403);
        }
        
        $post->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully.'
        ]);
    }

    public function approve(Post $post)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $post->update(['status' => 'approved']);
        
        return response()->json([
            'success' => true,
            'message' => 'Post approved successfully.',
            'post' => $post
        ]);
    }

    public function publish(Post $post)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $post->update([
            'status' => 'published',
            'published_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Post published successfully.',
            'post' => $post
        ]);
    }

    public function archive(Post $post)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $post->update(['status' => 'archived']);
        
        return response()->json([
            'success' => true,
            'message' => 'Post archived successfully.',
            'post' => $post
        ]);
    }

    public function like(Post $post)
    {
        // Check if user can view this post
        $user = auth()->user();
        if ($post->status !== 'published' && $user->role !== 'admin' && $user->id !== $post->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        // Increment like count
        $post->increment('likes');
        
        return response()->json([
            'success' => true,
            'message' => 'Post liked successfully!',
            'likes' => $post->likes
        ]);
    }
}