<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Post $post)
    {
        return $user->role === 'admin' || $user->id === $post->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Post $post)
    {
        return $user->role === 'admin' || ($user->id === $post->user_id && $post->status === 'pending');
    }

    public function delete(User $user, Post $post)
    {
        return $user->role === 'admin';
    }

    public function approve(User $user, Post $post)
    {
        return $user->role === 'admin';
    }

    public function publish(User $user, Post $post)
    {
        return $user->role === 'admin';
    }

    public function archive(User $user, Post $post)
    {
        return $user->role === 'admin';
    }
}