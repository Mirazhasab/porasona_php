<?php

namespace Database\Factories;

use App\Models\McqQuestion;
use App\Models\McqSet;
use Illuminate\Database\Eloquent\Factories\Factory;

class McqQuestionFactory extends Factory
{
    protected $model = McqQuestion::class;

    public function definition(): array
    {
        return [
            'mcq_set_id' => McqSet::factory(),
            'question' => $this->faker->sentence(8),
            'ans_1' => $this->faker->words(3, true),
            'ans_2' => $this->faker->words(3, true),
            'ans_3' => $this->faker->words(3, true),
            'ans_4' => $this->faker->words(3, true),
            'correct_ans' => '1',
            'marks' => 1,
        ];
    }
}
