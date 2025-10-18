# cPanel Deployment Guide - CSS/JS Asset Loading

## Problem Solved
CSS and JS files now load correctly on both localhost and cPanel hosting with proper cache-busting.

## What Changed

### 1. Asset Path Strategy
- **Before**: Multiple loading methods (asset/url/root) causing duplicates
- **After**: Single `asset()` helper with cache-busting version parameter
- **Benefit**: Works consistently on localhost and cPanel, forces browser refresh on updates

### 2. Cache-Busting Implementation
All CSS/JS now use: `{{ asset('path/to/file.css') }}?v={{ filemtime(public_path('path/to/file.css')) }}`

This ensures:
- Browsers reload files when they change
- No stale cached versions on cPanel
- Unique version for each file modification

### 3. Files Updated
- ✅ `resources/views/layouts/mcq.blade.php`
- ✅ `resources/views/layouts/app.blade.php`
- ✅ `resources/views/layouts/admin.blade.php`
- ✅ `resources/views/layouts/user.blade.php`
- ✅ `resources/views/layouts/modern.blade.php`

## cPanel Deployment Checklist

### Step 0: **CRITICAL - Sync Assets First** (Run Locally)
```bash
# Windows:
cd j:\php_mcq_app
.\sync_assets_to_cpanel.bat

# This copies CSS/JS from mcq_pro/public/ to public_html/
# Must be done BEFORE uploading to cPanel!
```

### Step 1: Upload Files to cPanel
```bash
# Upload BOTH folders to your hosting:
# /home/username/mcq_pro/           (Laravel app)
# /home/username/public_html/       (Web root with assets)

# IMPORTANT: public_html MUST contain:
# - index.php (bootstrap file)
# - .htaccess (rewrite rules)
# - css/ folder (copied from mcq_pro/public/css)
# - js/ folder (copied from mcq_pro/public/js)
```

### Step 2: Verify public_html/index.php
Ensure it points correctly to mcq_pro:
```php
require __DIR__.'/../mcq_pro/vendor/autoload.php';
$app = require_once __DIR__.'/../mcq_pro/bootstrap/app.php';
```

### Step 2b: Update .env File
**CRITICAL**: Change APP_URL to your domain:
```env
# In mcq_pro/.env
APP_URL=https://prosonahub.xyz

# NOT localhost:8000!
```

### Step 3: Set Permissions
```bash
# In cPanel Terminal or SSH:
cd ~/mcq_pro
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public

# Also set permissions for public_html assets:
cd ~/public_html
chmod -R 755 css
chmod -R 755 js
```

### Step 4: Clear Laravel Caches (SSH or cPanel Terminal)
```bash
cd ~/mcq_pro
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 5: Verify .htaccess Files
Both `public_html/.htaccess` and `mcq_pro/public/.htaccess` should have:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Step 6: Test Asset Loading
Visit your site and check browser console (F12):
- ✅ No 404 errors for CSS files
- ✅ No 404 errors for JS files
- ✅ All files load with ?v=timestamp parameter

## Troubleshooting

### Assets Still Not Loading?

**Check 0: DID YOU SYNC ASSETS?**
```bash
# Assets must be in public_html, not just mcq_pro/public!
ls -la ~/public_html/css/
ls -la ~/public_html/js/

# Should show all CSS/JS files
# If empty, run sync_assets_to_cpanel.bat locally first!
```

**Check 1: Is APP_URL Correct?**
```bash
# In mcq_pro/.env:
APP_URL=https://prosonahub.xyz  # ← Must match your domain!

# Then clear config:
cd ~/mcq_pro
php artisan config:clear
```

**Check 2: File Permissions**
```bash
chmod -R 755 ~/public_html/css
chmod -R 755 ~/public_html/js
```

**Check 2: Symlink (if using storage link)**
```bash
php artisan storage:link
```

**Check 3: Clear Browser Cache**
- Press Ctrl+Shift+Delete
- Clear cached files
- Hard refresh: Ctrl+F5

**Check 4: Check Asset Paths in Browser**
Right-click page → View Source
Look for CSS/JS links - they should look like:
```html
<link href="https://yourdomain.com/css/asset-loader.css?v=1234567890">
<script src="https://yourdomain.com/js/global-fixes.js?v=1234567890">
```

### Common cPanel Issues Fixed

1. **Stale Cache**: Cache-busting version parameter forces reload
2. **Path Resolution**: Using Laravel's `asset()` ensures correct absolute URLs
3. **File Not Found**: All files verified to exist with `filemtime()` or fallback to `time()`
4. **Duplicate Loads**: Removed multiple loading methods

## Verification Commands

### Check if files exist:
```bash
ls -la ~/mcq_pro/public/css/
ls -la ~/mcq_pro/public/js/
```

### Check file permissions:
```bash
ls -la ~/mcq_pro/public/css/asset-loader.css
ls -la ~/mcq_pro/public/js/global-fixes.js
```

### Test asset URLs:
```bash
curl -I https://yourdomain.com/css/asset-loader.css
curl -I https://yourdomain.com/js/global-fixes.js
```

Should return: `HTTP/1.1 200 OK`

## Localhost vs cPanel

### Both Work Because:
1. **Laravel's asset() helper** generates correct URLs for both environments
2. **Cache-busting** prevents stale files
3. **Single source of truth** for asset paths
4. **No hardcoded paths** that break between environments

### Environment-Specific:
- **Localhost**: `http://localhost:8000/css/file.css?v=123`
- **cPanel**: `https://yourdomain.com/css/file.css?v=123`

Both use the same Blade templates, just different base URLs.

## Need Help?

If CSS/JS still not loading:
1. Check browser console for exact error
2. Verify file exists at the path shown in error
3. Check file permissions (should be 644 or 755)
4. Clear all caches (Laravel + browser)
5. Verify .env APP_URL matches your domain

## Success Indicators

✅ Page loads with all styles applied
✅ Interactive elements (buttons, dropdowns) work
✅ No console errors for missing files
✅ Browser shows ?v=timestamp on all asset URLs
✅ Refreshing page loads latest versions of files

---
**Last Updated**: October 13, 2025
**Compatible With**: Laravel 11+, cPanel Shared Hosting
