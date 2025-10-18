<?php

namespace App\Helpers;

class AssetVersionHelper
{
    /**
     * Get asset URL with cache-busting version parameter
     * Works for both localhost and cPanel deployments
     * 
     * @param string $path Path to asset (e.g., 'css/style.css')
     * @return string Full asset URL with version parameter
     */
    public static function asset($path)
    {
        // Get the base asset URL using Laravel's asset() helper
        $url = asset($path);
        
        // Add version based on file modification time for cache busting
        $publicPath = public_path($path);
        
        if (file_exists($publicPath)) {
            $version = filemtime($publicPath);
            $separator = strpos($url, '?') === false ? '?' : '&';
            $url .= $separator . 'v=' . $version;
        } else {
            // Fallback to timestamp if file doesn't exist
            $url .= (strpos($url, '?') === false ? '?' : '&') . 'v=' . time();
        }
        
        return $url;
    }
    
    /**
     * Get multiple asset URLs with cache-busting
     * 
     * @param array $paths Array of asset paths
     * @return array Array of versioned URLs
     */
    public static function assets(array $paths)
    {
        return array_map([self::class, 'asset'], $paths);
    }
}
