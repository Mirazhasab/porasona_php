# 🚨 404 Error Fix - CSS/JS Not Found on cPanel

## The Problem
```
404 Not Found
https://prosonahub.xyz/css/asset-loader.css?v=1234567890
```

## Root Cause
**Assets are in `mcq_pro/public/` but cPanel web root is `public_html/`**

Laravel's `asset()` helper generates URLs like:
- `https://prosonahub.xyz/css/file.css`

But the files are located at:
- `~/mcq_pro/public/css/file.css` ❌ (Not accessible)

They need to be at:
- `~/public_html/css/file.css` ✅ (Accessible)

## The Fix - 3 Steps

### Step 1: Sync Assets Locally (Before Upload)
```bash
# Run on your local machine:
cd j:\php_mcq_app
.\sync_assets_to_cpanel.bat
```

This copies:
- `mcq_pro/public/css/` → `public_html/css/`
- `mcq_pro/public/js/` → `public_html/js/`

### Step 2: Update .env on cPanel
```env
# Edit mcq_pro/.env:
APP_URL=https://prosonahub.xyz  # ← Change from localhost!
APP_ENV=production
APP_DEBUG=false
```

### Step 3: Clear Laravel Caches
```bash
# In cPanel Terminal or SSH:
cd ~/mcq_pro
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## Verify Fix Worked

### Test 1: Check Files Exist
```bash
# SSH into cPanel:
ls -la ~/public_html/css/
# Should show: asset-loader.css, global-fixes.css, etc.

ls -la ~/public_html/js/
# Should show: global-fixes.js, spa-system.js, etc.
```

### Test 2: Test Asset URLs Directly
Visit these URLs in your browser:
- `https://prosonahub.xyz/css/asset-loader.css`
- `https://prosonahub.xyz/js/global-fixes.js`

Should see CSS/JS code, not 404 error.

### Test 3: Check Browser Console
1. Open your site: `https://prosonahub.xyz`
2. Press F12 → Network tab
3. Refresh page (Ctrl+F5)
4. Filter by "css" and "js"
5. All should show **200 OK** status

## Still Getting 404?

### Checklist:
- [ ] Ran `sync_assets_to_cpanel.bat` locally?
- [ ] Uploaded `public_html/css/` folder to cPanel?
- [ ] Uploaded `public_html/js/` folder to cPanel?
- [ ] Updated `APP_URL` in `.env`?
- [ ] Set `APP_ENV=production` in `.env`?
- [ ] Ran `php artisan config:clear`?
- [ ] Cleared browser cache (Ctrl+Shift+Del)?
- [ ] Hard refreshed page (Ctrl+F5)?

### Debug Commands:
```bash
# 1. Check public_html structure:
ls -la ~/public_html/
# Should show: css/, js/, index.php, .htaccess

# 2. Check file permissions:
ls -la ~/public_html/css/asset-loader.css
# Should be: -rw-r--r-- (644) or -rwxr-xr-x (755)

# 3. Test file accessibility:
curl -I https://prosonahub.xyz/css/asset-loader.css
# Should return: HTTP/1.1 200 OK

# 4. Check .env:
cat ~/mcq_pro/.env | grep APP_URL
# Should show: APP_URL=https://prosonahub.xyz
```

## Understanding the Structure

### cPanel Layout:
```
/home/username/
├── public_html/           ← Web root (what Apache serves)
│   ├── index.php         ← Bootstraps Laravel from mcq_pro/
│   ├── .htaccess         ← Rewrite rules
│   ├── css/              ← CSS files (copied from mcq_pro/public/)
│   ├── js/               ← JS files (copied from mcq_pro/public/)
│   └── robots.txt
│
└── mcq_pro/              ← Laravel application
    ├── app/
    ├── config/
    ├── public/           ← Original assets (NOT served by Apache)
    │   ├── css/
    │   └── js/
    ├── resources/
    └── .env              ← Configure APP_URL here!
```

### Why This Structure?
- cPanel serves files from `public_html/` (web root)
- Laravel files are in `mcq_pro/` (outside web root for security)
- `public_html/index.php` bootstraps from `../mcq_pro/`
- Assets must be copied to `public_html/` to be accessible

## Automation for Future Updates

When you update CSS/JS files:

1. **Edit** files in `mcq_pro/public/css/` or `mcq_pro/public/js/`
2. **Run** `sync_assets_to_cpanel.bat` locally
3. **Upload** only the changed files to cPanel `public_html/`
4. **Clear** Laravel cache: `php artisan config:clear`

## Prevention

To avoid this issue in the future:

1. Always run `sync_assets_to_cpanel.bat` before uploading
2. Keep `.env.cpanel.example` updated with correct domain
3. Test locally first with correct APP_URL
4. Document any new asset files added

---

## Quick Fix Summary

```bash
# LOCAL MACHINE:
cd j:\php_mcq_app
.\sync_assets_to_cpanel.bat

# UPLOAD TO CPANEL:
# - public_html/ folder (with css/ and js/)
# - mcq_pro/ folder

# CPANEL TERMINAL:
cd ~/mcq_pro
nano .env  # Update APP_URL=https://prosonahub.xyz
php artisan config:clear

# BROWSER:
# Press Ctrl+Shift+Del → Clear cache
# Visit: https://prosonahub.xyz
# Should work now! ✅
```

---
**Last Updated**: October 13, 2025
**Issue**: 404 errors for CSS/JS on cPanel
**Solution**: Sync assets to public_html + Update APP_URL
