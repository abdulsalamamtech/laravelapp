<?php

namespace Database\Seeders;

use App\Models\System\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DefaultRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['super_admin', 'admin', 'company', 'manager', 'content-manager'];
        foreach ($roles as $role) {
            if (! Role::where('name', $role)->exists()) {
                Log::info("Role seeder - Creating Role: {$role}");
                Role::create([
                    'name' => $role,
                    'guard_name' => 'web',
                ]);
                Log::info("Role seeder - Role Created: {$role}");
            } else {
                Log::alert("Role seeder - Role already exist: {$role}");
            }
        }
    }
}
