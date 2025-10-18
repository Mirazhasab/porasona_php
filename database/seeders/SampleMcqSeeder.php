<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\McqSet;
use App\Models\McqQuestion;
use App\Models\User;

class SampleMcqSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }

        if ($admin) {
            // Create Mathematics Quiz
            $mathQuiz = McqSet::create([
                'user_id' => $admin->id,
                'title' => 'Mathematics Quiz',
                'exam_name' => 'Mathematics Quiz',
                'exam_date' => now()->format('Y-m-d'),
                'exam_time' => '10:00:00',
                'total_marks' => 10,
                'duration' => 60,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create Science Test
            $scienceTest = McqSet::create([
                'user_id' => $admin->id,
                'title' => 'Science Test',
                'exam_name' => 'Science Test',
                'exam_date' => now()->addDays(1)->format('Y-m-d'),
                'exam_time' => '14:00:00',
                'total_marks' => 15,
                'duration' => 90,
                'status' => 'approved',
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1)
            ]);

            // Create English Quiz
            $englishQuiz = McqSet::create([
                'user_id' => $admin->id,
                'title' => 'English Language Quiz',
                'exam_name' => 'English Language Quiz',
                'exam_date' => now()->addDays(2)->format('Y-m-d'),
                'exam_time' => '09:00:00',
                'total_marks' => 8,
                'duration' => 45,
                'status' => 'approved',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2)
            ]);

            // Add some sample questions to Mathematics Quiz
            McqQuestion::create([
                'mcq_set_id' => $mathQuiz->id,
                'question' => 'What is 2 + 2?',
                'ans_1' => '3',
                'ans_2' => '4',
                'ans_3' => '5',
                'ans_4' => '6',
                'correct_ans' => '2',
                'marks' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            McqQuestion::create([
                'mcq_set_id' => $mathQuiz->id,
                'question' => 'What is the square root of 16?',
                'ans_1' => '2',
                'ans_2' => '4',
                'ans_3' => '6',
                'ans_4' => '8',
                'correct_ans' => '2',
                'marks' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Add questions to Science Test
            McqQuestion::create([
                'mcq_set_id' => $scienceTest->id,
                'question' => 'What is the chemical symbol for water?',
                'ans_1' => 'H2O',
                'ans_2' => 'CO2',
                'ans_3' => 'O2',
                'ans_4' => 'N2',
                'correct_ans' => '1',
                'marks' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Add questions to English Quiz
            McqQuestion::create([
                'mcq_set_id' => $englishQuiz->id,
                'question' => 'Which of the following is a noun?',
                'ans_1' => 'Run',
                'ans_2' => 'Beautiful',
                'ans_3' => 'Book',
                'ans_4' => 'Quickly',
                'correct_ans' => '3',
                'marks' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
