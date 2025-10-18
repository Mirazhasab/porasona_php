<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\User;

class PostManagementSeeder extends Seeder
{
    public function run()
    {
        // Create categories
        $categories = [
            ['name' => 'Technology', 'description' => 'Posts about technology and programming'],
            ['name' => 'Education', 'description' => 'Educational content and tutorials'],
            ['name' => 'News', 'description' => 'Latest news and updates'],
            ['name' => 'Tutorial', 'description' => 'Step-by-step tutorials'],
            ['name' => 'Review', 'description' => 'Product and service reviews'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }

        // Create tags
        $tags = [
            'Laravel', 'PHP', 'JavaScript', 'Vue.js', 'React', 'MySQL', 
            'Web Development', 'Programming', 'Tutorial', 'Tips', 
            'Best Practices', 'Security', 'Performance', 'Database'
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }

        // Create demo posts if users exist
        $users = User::all();
        $categories = Category::all();
        $tags = Tag::all();

        if ($users->count() > 0 && $categories->count() > 0) {
            $demoPosts = [
                [
                    'title' => 'Getting Started with Laravel',
                    'content' => '<h2>Introduction to Laravel</h2><p>Laravel is a powerful PHP framework that makes web development enjoyable and creative. In this post, we\'ll explore the basics of Laravel and how to get started.</p><h3>Installation</h3><p>To install Laravel, you can use Composer...</p>',
                    'status' => 'published',
                    'published_at' => now()->subDays(5),
                ],
                [
                    'title' => 'Advanced PHP Techniques',
                    'content' => '<h2>Modern PHP Development</h2><p>PHP has evolved significantly over the years. Let\'s explore some advanced techniques that can improve your code quality and performance.</p>',
                    'status' => 'pending',
                ],
                [
                    'title' => 'JavaScript ES6 Features',
                    'content' => '<h2>ES6 and Beyond</h2><p>JavaScript ES6 introduced many powerful features that make development more efficient. Here are some key features you should know...</p>',
                    'status' => 'approved',
                ],
            ];

            foreach ($demoPosts as $postData) {
                $post = Post::create([
                    'user_id' => $users->random()->id,
                    'category_id' => $categories->random()->id,
                    'title' => $postData['title'],
                    'content' => $postData['content'],
                    'status' => $postData['status'],
                    'published_at' => $postData['published_at'] ?? null,
                ]);

                // Attach random tags
                $randomTags = $tags->random(rand(2, 4));
                $post->tags()->attach($randomTags->pluck('id'));
            }
        }
    }
}