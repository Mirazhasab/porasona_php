<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User base permissions (all users have these)
            'view posts',
            'create posts',
            'edit own posts',
            'delete own posts',
            'view profile',
            'edit own profile',
            'view comments',
            'create comments',
            
            // Moderator permissions (assigned by admin)
            'moderate comments',
            'delete any comment',
            'approve posts',
            'view reports',
            'manage tags',
            
            // Admin permissions (full control)
            'manage users',
            'assign roles',
            'manage permissions',
            'delete any post',
            'view all analytics',
            'manage settings',
            'view admin panel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create roles and assign permissions
        $userRolePermissions = [
            'view posts',
            'create posts',
            'edit own posts',
            'delete own posts',
            'view profile',
            'edit own profile',
            'view comments',
            'create comments',
        ];

        // 1. User Role - Base permissions
        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);
        $userRole->syncPermissions($userRolePermissions);

        // 2. Moderator Role - User permissions + moderator permissions
        $moderatorRolePermissions = array_merge($userRolePermissions, [
            'moderate comments',
            'delete any comment',
            'approve posts',
            'view reports',
            'manage tags',
        ]);
        $moderatorRole = Role::firstOrCreate([
            'name' => 'moderator',
            'guard_name' => 'web',
        ]);
        $moderatorRole->syncPermissions($moderatorRolePermissions);

        // 3. Admin Role - All permissions
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions(Permission::pluck('name')->all());

        // Create a default admin user if needed
        $adminUser = User::where('email', 'admin@mcqpro.com')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Admin User',
                'email' => 'admin@mcqpro.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]);
        }
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        $this->command->info('Roles and Permissions have been seeded successfully!');
        $this->command->info('Default Admin: admin@mcqpro.com / password123');
    }
}
