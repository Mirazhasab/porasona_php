#!/bin/bash

# MCQ Pro Deployment Script for cPanel
echo "🚀 MCQ Pro Deployment Script"
echo "=============================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

echo "🔑 Generating application key..."
php artisan key:generate --force

echo "🗄️ Running database migrations..."
php artisan migrate --force

echo "🔗 Creating storage link..."
php artisan storage:link

echo "👤 Creating admin user..."
php artisan tinker --execute="
\$user = App\Models\User::firstOrCreate(
    ['email' => 'admin@mcqpro.com'],
    [
        'name' => 'Admin',
        'password' => bcrypt('admin123'),
        'role' => 'admin',
        'email_verified_at' => now()
    ]
);
echo 'Admin user created: ' . \$user->email;
"

echo "🛡️ Seeding roles and permissions..."
php artisan db:seed --class=RolesAndPermissionsSeeder --force

echo "🧹 Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "📁 Setting permissions..."
chmod -R 755 storage bootstrap/cache

echo "✅ Deployment completed successfully!"
echo ""
echo "🎉 Your MCQ Pro system is ready!"
echo "📧 Admin Email: admin@mcqpro.com"
echo "🔐 Admin Password: admin123"
echo "🌐 Please change the admin password after first login"
echo ""