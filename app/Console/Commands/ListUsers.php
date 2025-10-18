<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users with their roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all(['id', 'name', 'email', 'role', 'created_at']);
        
        if ($users->isEmpty()) {
            $this->warn("No users found in database.");
            return 0;
        }
        
        $this->info("Total Users: " . $users->count());
        $this->info("Admins: " . $users->where('role', 'admin')->count());
        $this->info("Regular Users: " . $users->where('role', 'user')->count());
        $this->newLine();
        
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Joined'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role ?? 'user',
                    $user->created_at->format('Y-m-d H:i'),
                ];
            })
        );
        
        return 0;
    }
}
