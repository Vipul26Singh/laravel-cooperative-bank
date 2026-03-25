<?php

namespace Database\Seeders;

use App\Models\CompanySetup;
use Illuminate\Database\Seeder;

class CompanySetupSeeder extends Seeder
{
    public function run(): void
    {
        \DB::table('company_setup')->insertOrIgnore([
            'name'       => 'Cooperative Bank',
            'address'    => '123 Main Street, City',
            'phone'      => '+91-9999999999',
            'email'      => 'info@cooperativebank.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
