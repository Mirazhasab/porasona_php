<?php

namespace Database\Factories;

use App\Models\McqSet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class McqSetFactory extends Factory
{
    protected $model = McqSet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'category' => $this->faker->randomElement(['Science', 'Mathematics', 'English']),
            'exam_name' => $this->faker->sentence(2),
            'total_marks' => 100,
            'duration' => 60,
            'status' => 'approved',
        ];
    }
}
