<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckUnused extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:unused';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan Laravel project for used and unused Views, Controllers, and Models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Scanning project for used and unused files...');

        // === 1️⃣ Views ===
        $usedViews = [];
        foreach (File::allFiles(app_path()) as $file) {
            preg_match_all("/view\(['\"](.*?)['\"]\)/", $file->getContents(), $matches);
            $usedViews = array_merge($usedViews, $matches[1]);
        }
        foreach (File::allFiles(base_path('routes')) as $file) {
            preg_match_all("/view\(['\"](.*?)['\"]\)/", $file->getContents(), $matches);
            $usedViews = array_merge($usedViews, $matches[1]);
        }

        $usedViews = array_unique($usedViews);

        $allViews = collect(File::allFiles(resource_path('views')))
            ->map(fn($f) => str_replace(['.blade.php', '/'], ['', '.'], $f->getRelativePathname()));

        $unusedViews = $allViews->diff($usedViews);
        $usedViewList = $allViews->intersect($usedViews);

        // === 2️⃣ Controllers ===
        $controllerFiles = collect(File::allFiles(app_path('Http/Controllers')))
            ->map(fn($f) => str_replace('/', '\\', 'App\\Http\\Controllers\\' . str_replace('.php', '', $f->getRelativePathname())));

        $usedControllers = [];
        foreach (File::allFiles(base_path('routes')) as $file) {
            preg_match_all("/App\\\\Http\\\\Controllers\\\\[A-Za-z0-9_\\\\]+/", $file->getContents(), $matches);
            $usedControllers = array_merge($usedControllers, $matches[0]);
        }

        $usedControllers = array_unique($usedControllers);
        $unusedControllers = $controllerFiles->diff($usedControllers);
        $usedControllerList = $controllerFiles->intersect($usedControllers);

        // === 3️⃣ Models ===
        $modelFiles = collect(File::allFiles(app_path('Models')))
            ->map(fn($f) => str_replace('/', '\\', 'App\\Models\\' . str_replace('.php', '', $f->getRelativePathname())));

        $usedModels = [];
        foreach (File::allFiles(app_path()) as $file) {
            preg_match_all("/App\\\\Models\\\\[A-Za-z0-9_\\\\]+/", $file->getContents(), $matches);
            $usedModels = array_merge($usedModels, $matches[0]);
        }

        $usedModels = array_unique($usedModels);
        $unusedModels = $modelFiles->diff($usedModels);
        $usedModelList = $modelFiles->intersect($usedModels);

        // === 🧾 Results ===
        $this->newLine();
        $this->info('✅ Used Views:');
        foreach ($usedViewList as $view) {
            $this->line('  - ' . $view);
        }

        $this->newLine();
        $this->warn('🟠 Unused Views:');
        foreach ($unusedViews as $view) {
            $this->line('  - ' . $view);
        }

        $this->newLine();
        $this->info('✅ Used Controllers:');
        foreach ($usedControllerList as $controller) {
            $this->line('  - ' . $controller);
        }

        $this->newLine();
        $this->warn('🟣 Unused Controllers:');
        foreach ($unusedControllers as $controller) {
            $this->line('  - ' . $controller);
        }

        $this->newLine();
        $this->info('✅ Used Models:');
        foreach ($usedModelList as $model) {
            $this->line('  - ' . $model);
        }

        $this->newLine();
        $this->warn('🟢 Unused Models:');
        foreach ($unusedModels as $model) {
            $this->line('  - ' . $model);
        }

        $this->newLine();
        $this->info('✨ Scan complete!');
    }
}
