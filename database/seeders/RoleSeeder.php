<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'SuperAdmin', 'display_name' => 'Super Administrator', 'description' => 'Full system access'],
            ['name' => 'Manager',    'display_name' => 'Branch Manager',       'description' => 'Branch level management and approvals'],
            ['name' => 'Clerk',      'display_name' => 'Bank Clerk',           'description' => 'Customer entry and loan applications'],
            ['name' => 'Cashier',    'display_name' => 'Bank Cashier',         'description' => 'Cash and transaction processing'],
            ['name' => 'Accountant', 'display_name' => 'Accountant',           'description' => 'Financial reporting and reconciliation'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
