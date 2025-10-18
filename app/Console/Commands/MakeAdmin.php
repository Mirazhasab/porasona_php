<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email : The email of the user to make admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user an admin by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found!");
            
            // Show all users
            $this->info("\nAvailable users:");
            $users = User::all(['id', 'name', 'email', 'role']);
            
            if ($users->isEmpty()) {
                $this->warn("No users found in database.");
                return 1;
            }
            
            $this->table(
                ['ID', 'Name', 'Email', 'Role'],
                $users->map(function ($user) {
                    return [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->role ?? 'user',
                    ];
                })
            );
            
            return 1;
        }
        
        if ($user->role === 'admin') {
            $this->info("User '{$user->name}' is already an admin!");
            return 0;
        }
        
        $user->role = 'admin';
        $user->save();
        
        $this->info("✅ Successfully made '{$user->name}' an admin!");
        
        return 0;
    }
}
