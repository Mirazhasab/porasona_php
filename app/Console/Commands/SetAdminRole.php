<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SetAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:set {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set admin role for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            // Set first user as admin
            $user = User::first();
        } else {
            $user = User::where('email', $email)->first();
        }
        
        if (!$user) {
            $this->error('User not found');
            return;
        }
        
        $user->role = 'admin';
        $user->save();
        
        $this->info("User {$user->name} ({$user->email}) is now admin");
        $this->info("Role column: {$user->role}");
    }
}
