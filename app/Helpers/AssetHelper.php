<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AssetHelper
{
    /**
     * Get the current page CSS file path if it exists
     * 
     * @return string|null
     */
    public static function getPageSpecificCss(): ?string
    {
        // Get the current route name or view name
        $routeName = request()->route()?->getName();
        
        if (!$routeName) {
            return null;
        }
        
        // Check for component-specific CSS first (for auth routes)
        if (Str::startsWith($routeName, ['auth.', 'livewire.'])) {
            $componentCss = self::getLivewireComponentCss($routeName);
            if ($componentCss) {
                return $componentCss;
            }
        }
        
        // Convert route name to CSS filename for pages
        // Examples: 
        // 'mcq.dashboard' -> 'dashboard.css'
        // 'mcq_sets.index' -> 'mcq-sets-index.css'
        $cssFileName = self::routeToCssFileName($routeName);
        
        // Check if CSS file exists in pages directory
        $cssPath = public_path("css/pages/{$cssFileName}");
        
        if (File::exists($cssPath)) {
            return "css/pages/{$cssFileName}";
        }
        
        return null;
    }
    
    /**
     * Convert route name to CSS filename
     * 
     * @param string $routeName
     * @return string
     */
    private static function routeToCssFileName(string $routeName): string
    {
        // Remove common prefixes and convert to kebab-case
        $name = Str::of($routeName)
            ->replace(['mcq.', 'auth.', 'admin.'], '')
            ->replace('.', '-')
            ->replace('_', '-')
            ->kebab()
            ->toString();
            
        return "{$name}.css";
    }
    
    /**
     * Get page-specific CSS for Livewire components
     * 
     * @param string $componentName
     * @return string|null
     */
    public static function getLivewireComponentCss(string $componentName): ?string
    {
        // Convert component name to CSS filename
        // Example: 'auth.login' -> 'auth-login.css'
        $cssFileName = Str::of($componentName)
            ->replace('.', '-')
            ->kebab()
            ->append('.css')
            ->toString();
            
        $cssPath = public_path("css/components/{$cssFileName}");
        
        if (File::exists($cssPath)) {
            return "css/components/{$cssFileName}";
        }
        
        return null;
    }
    
    /**
     * Get all page-specific CSS files for the current request
     * 
     * @return array
     */
    public static function getAllPageCss(): array
    {
        $cssFiles = [];
        
        // Get route-based CSS
        if ($routeCss = self::getPageSpecificCss()) {
            $cssFiles[] = $routeCss;
        }
        
        // Get view-based CSS (fallback)
        if (view()->exists(request()->route()?->getName() ?? '')) {
            $viewName = str_replace('.', '-', request()->route()?->getName() ?? '');
            $viewCssPath = public_path("css/views/{$viewName}.css");
            
            if (File::exists($viewCssPath)) {
                $cssFiles[] = "css/views/{$viewName}.css";
            }
        }
        
        return array_unique($cssFiles);
    }
    
    /**
     * Render page-specific CSS link tags
     * 
     * @return string
     */
    public static function renderPageCss(): string
    {
        $cssFiles = self::getAllPageCss();
        $output = '';
        
        foreach ($cssFiles as $cssFile) {
            $version = self::getCssVersion($cssFile);
            $output .= '<link rel="stylesheet" href="' . asset($cssFile) . '?v=' . $version . '">' . "\n    ";
        }
        
        return $output;
    }
    
    /**
     * Get CSS file version for cache busting
     * 
     * @param string $cssFile
     * @return string
     */
    private static function getCssVersion(string $cssFile): string
    {
        $filePath = public_path($cssFile);
        
        if (File::exists($filePath)) {
            return (string) File::lastModified($filePath);
        }
        
        return (string) time();
    }
}
