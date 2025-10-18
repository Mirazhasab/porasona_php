<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;

class PostSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }

        $categories = Category::all();
        
        if ($categories->count() > 0 && $admin) {
            Post::create([
                'user_id' => $admin->id,
                'category_id' => $categories->first()->id,
                'title' => 'Welcome to MCQ PRO!',
                'content' => 'We are excited to announce the launch of our new MCQ management system. This platform will help students practice and take exams efficiently.',
                'status' => 'published',
                'published_at' => now(),
                'views' => 25,
                'likes' => 5
            ]);
            
            Post::create([
                'user_id' => $admin->id,
                'category_id' => $categories->skip(1)->first()->id ?? $categories->first()->id,
                'title' => 'New Mathematics Exam Available',
                'content' => 'A new mathematics exam covering algebra and geometry topics is now available. The exam consists of 25 questions and has a time limit of 60 minutes.',
                'status' => 'published',
                'published_at' => now()->subHours(2),
                'views' => 18,
                'likes' => 3
            ]);
            
            Post::create([
                'user_id' => $admin->id,
                'category_id' => $categories->skip(2)->first()->id ?? $categories->first()->id,
                'title' => 'Study Tips for Better Performance',
                'content' => 'Here are some effective study tips to help you perform better in your exams: 1. Create a study schedule, 2. Practice regularly, 3. Take breaks, 4. Review your mistakes.',
                'status' => 'published',
                'published_at' => now()->subHours(5),
                'views' => 32,
                'likes' => 8
            ]);
        }
    }
}
