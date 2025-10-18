<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Announcements',
                'description' => 'Important announcements and news',
                'color' => '#3B82F6'
            ],
            [
                'name' => 'Exam Updates',
                'description' => 'Exam schedules and updates',
                'color' => '#EF4444'
            ],
            [
                'name' => 'Study Materials',
                'description' => 'Study resources and materials',
                'color' => '#10B981'
            ],
            [
                'name' => 'Events',
                'description' => 'Upcoming events and activities',
                'color' => '#F59E0B'
            ],
            [
                'name' => 'General',
                'description' => 'General discussions and posts',
                'color' => '#6B7280'
            ]
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'color' => $category['color'],
                'is_active' => true
            ]);
        }
    }
}
