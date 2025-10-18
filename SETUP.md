# Setup Instructions

## Localhost Development Setup

1. **Copy the environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Update the `.env` file with your local database credentials:**
   - Set `DB_HOST` to `localhost`
   - Set `DB_NAME` to your local database name
   - Set `DB_USER` to your local database username
   - Set `DB_PASS` to your local database password
   - Keep `APP_ENV=development` and `APP_DEBUG=true`

3. **Copy the .htaccess file:**
   ```bash
   cp .htaccess.example .htaccess
   ```

4. **Start your local server** (e.g., XAMPP, WAMP, or PHP built-in server)

## cPanel Deployment Setup

1. **Upload your project files** to your cPanel hosting via FTP/SFTP or File Manager

2. **Create the `.env` file** on your cPanel server:
   - Copy `.env.example` to `.env`
   - Update database credentials (typically: `cpanel_username_dbname`, `cpanel_username_dbuser`)
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Set `APP_URL` to your domain

3. **Create the `.htaccess` file** on your cPanel server:
   - Copy `.htaccess.example` to `.htaccess`
   - Uncomment HTTPS redirect lines if you have SSL certificate
   - Adjust PHP settings if needed based on your hosting plan

## Important Notes

- **Never commit `.env` or `.htaccess` files** to the repository
- These files are listed in `.gitignore` and will be automatically ignored by Git
- Always keep your `.env.example` and `.htaccess.example` files updated with the latest structure
- Each environment (localhost, cPanel) will have its own `.env` and `.htaccess` files
