<?php

namespace Database\Seeders;

use App\Models\System\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $adminEmail = ['admin@laravel.com']; // You can add more admin emails here
        // $adminRole = 'super_admin';
        // $adminPassword = 'password';
        // $viewAnyUserPermission = 'view_any_user';

        $roles1 = ['super_admin', 'admin'];
        // $roles2 = ['view_any_user', 'viewAny', 'deleteAny', 'updateAny', 'view', 'update', 'delete', 'create'];
        $roles2 = [];
        $roles = array_merge($roles1, $roles2);
        $adminAccounts = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password',
                'role' => 'super_admin',
            ],
        ];
        // check if super admin role exists, if not create it
        foreach ($roles as $role) {
            if (! Role::where('name', $role)->exists()) {
                Log::info("Creating role: {$role}");
                Role::create([
                    'name' => $role,
                    'guard_name' => 'web',
                ]);
            }

            Log::info("Created role: {$role}");
        }

        foreach ($adminAccounts as $account) {
            // check if admin user already exists
            if (User::where('email', $account['email'])->exists()) {
                Log::info("Admin user with email: {$account['email']} already exists.");

                continue; // skip to next email if admin already exists
            }

            // Create the user
            $user = User::create([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => bcrypt($account['password']), // default password
            ]);
            // check if super admin role exists, if not create it
            foreach ($roles as $role) {
                if (! Role::where('name', $role)->exists()) {
                    Log::info("Creating role: {$role}");
                    Role::create([
                        'name' => $role,
                        'guard_name' => 'web',
                    ]);
                }

                $user->assignRole($role);
                Log::info("Created admin user with email: {$account['email']} and role: {$role}");
            }
        }
    }
}
