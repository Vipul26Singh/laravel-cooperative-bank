<?php

namespace Database\Seeders;

use App\Models\{User, Role, Branch};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'SuperAdmin')->first();

        User::firstOrCreate(
            ['email' => 'admin@coopbank.com'],
            [
                'name'        => 'Super Administrator',
                'password'    => Hash::make('Admin@123'),
                'role_id'     => $superAdminRole?->id,
                'designation' => 'System Administrator',
                'employee_code' => 'EMP001',
                'is_active'   => true,
            ]
        );
    }
}
