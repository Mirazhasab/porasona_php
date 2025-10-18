<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserAccess;

class GrantReadAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mcq:grant-read-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant read access permission to all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Granting read access to all users...');
        
        $users = User::all();
        $updated = 0;
        
        foreach ($users as $user) {
            UserAccess::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'practice' => true,
                    'exams' => true,
                    'results' => true,
                    'classmate' => true,
                    'leaderboard' => true,
                    'post' => true,
                    'mcq_management' => false,
                    'read_access' => true,
                ]
            );
            $updated++;
        }
        
        $this->info("Successfully granted read access to {$updated} users.");
        
        return Command::SUCCESS;
    }
}
