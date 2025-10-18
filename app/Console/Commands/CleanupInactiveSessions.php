<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupInactiveSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup {--days=7 : Number of days to keep inactive sessions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up inactive and expired sessions from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("Cleaning up sessions older than {$days} days...");
        
        // Delete inactive sessions older than the cutoff date
        $deletedCount = DB::table('sessions')
            ->where('is_active', false)
            ->where('updated_at', '<', $cutoffDate)
            ->delete();
        
        // Also clean up sessions that haven't been active for a long time
        $expiredCount = DB::table('sessions')
            ->where('last_activity', '<', $cutoffDate->timestamp)
            ->delete();
        
        $totalDeleted = $deletedCount + $expiredCount;
        
        $this->info("Cleaned up {$totalDeleted} expired sessions.");
        
        // Clean up orphaned session files
        $this->cleanupSessionFiles();
        
        return Command::SUCCESS;
    }
    
    /**
     * Clean up orphaned session files
     */
    private function cleanupSessionFiles()
    {
        $sessionPath = storage_path('framework/sessions');
        
        if (!is_dir($sessionPath)) {
            return;
        }
        
        $files = glob($sessionPath . '/*');
        $deletedFiles = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $sessionId = basename($file);
                
                // Check if session exists in database
                $exists = DB::table('sessions')
                    ->where('id', $sessionId)
                    ->exists();
                
                if (!$exists) {
                    unlink($file);
                    $deletedFiles++;
                }
            }
        }
        
        if ($deletedFiles > 0) {
            $this->info("Cleaned up {$deletedFiles} orphaned session files.");
        }
    }
}
