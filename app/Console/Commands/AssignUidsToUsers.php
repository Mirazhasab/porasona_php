<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignUidsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-uids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign unique UIDs to existing users who don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting UID assignment for existing users...');

        // Get all users without UIDs
        $usersWithoutUids = User::whereNull('uid')->get();

        if ($usersWithoutUids->isEmpty()) {
            $this->info('All users already have UIDs assigned.');
            return;
        }

        $this->info("Found {$usersWithoutUids->count()} users without UIDs.");

        $progressBar = $this->output->createProgressBar($usersWithoutUids->count());
        $progressBar->start();

        $assignedCount = 0;

        foreach ($usersWithoutUids as $user) {
            try {
                $user->uid = User::generateUniqueUid();
                $user->save();
                $assignedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to assign UID to user ID {$user->id}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Successfully assigned UIDs to {$assignedCount} users.");
    }
}
