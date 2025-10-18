<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\McqSet;
use App\Models\McqQuestion;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class McqManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@mcqpro.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create Normal User
        $user = User::firstOrCreate(
            ['email' => 'user@mcqpro.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('user');

        // Create Admin's MCQ Set (Approved)
        $adminSet = McqSet::create([
            'user_id' => $admin->id,
            'title' => 'BCS 45th Preliminary MCQ',
            'category' => 'Government Job',
            'exam_name' => 'Bangladesh Civil Service Examination',
            'exam_date' => now()->addDays(30),
            'exam_time' => '10:00:00',
            'total_marks' => 100,
            'duration' => 120,
            'status' => 'approved',
        ]);

        // Add Questions to Admin's Set
        $question1 = McqQuestion::create([
            'mcq_set_id' => $adminSet->id,
            'question' => 'What is the capital of Bangladesh?',
            'ans_1' => 'Dhaka',
            'ans_2' => 'Chittagong',
            'ans_3' => 'Sylhet',
            'ans_4' => 'Rajshahi',
            'correct_ans' => '1',
            'marks' => 1,
        ]);

        $question2 = McqQuestion::create([
            'mcq_set_id' => $adminSet->id,
            'question' => 'Which year did Bangladesh gain independence?',
            'ans_1' => '1971',
            'ans_2' => '1947',
            'ans_3' => '1952',
            'ans_4' => '1969',
            'correct_ans' => '1',
            'marks' => 1,
        ]);

        $question3 = McqQuestion::create([
            'mcq_set_id' => $adminSet->id,
            'question' => 'What is the national language of Bangladesh?',
            'ans_1' => 'Bengali',
            'ans_2' => 'English',
            'ans_3' => 'Hindi',
            'ans_4' => 'Urdu',
            'correct_ans' => '1',
            'marks' => 1,
        ]);

        // Create User's MCQ Set (Pending)
        $userSet = McqSet::create([
            'user_id' => $user->id,
            'title' => 'General Knowledge Quiz 2025',
            'category' => 'General Knowledge',
            'exam_name' => 'Monthly General Knowledge Test',
            'exam_date' => now()->addDays(15),
            'exam_time' => '14:00:00',
            'total_marks' => 50,
            'duration' => 60,
            'status' => 'pending',
        ]);

        // Add Questions to User's Set
        $question4 = McqQuestion::create([
            'mcq_set_id' => $userSet->id,
            'question' => 'Who is known as the Father of the Nation in Bangladesh?',
            'ans_1' => 'Bangabandhu Sheikh Mujibur Rahman',
            'ans_2' => 'Ziaur Rahman',
            'ans_3' => 'Hussain Muhammad Ershad',
            'ans_4' => 'A.K. Fazlul Huq',
            'correct_ans' => '1',
            'marks' => 1,
        ]);

        $question5 = McqQuestion::create([
            'mcq_set_id' => $userSet->id,
            'question' => 'What is the currency of Bangladesh?',
            'ans_1' => 'Taka',
            'ans_2' => 'Rupee',
            'ans_3' => 'Dollar',
            'ans_4' => 'Pound',
            'correct_ans' => '1',
            'marks' => 1,
        ]);

        $question6 = McqQuestion::create([
            'mcq_set_id' => $userSet->id,
            'question' => 'Which river is known as the lifeline of Bangladesh?',
            'ans_1' => 'Padma',
            'ans_2' => 'Jamuna',
            'ans_3' => 'Meghna',
            'ans_4' => 'Karnaphuli',
            'correct_ans' => '1',
            'marks' => 1,
        ]);

        $this->command->info('MCQ Management sample data seeded successfully!');
        $this->command->info('Admin: admin@mcqpro.com / password123');
        $this->command->info('User: user@mcqpro.com / password123');
    }
}
