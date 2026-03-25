<?php

namespace Tests\Feature\Accountant;

use App\Models\{User, Role, Branch};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $accountant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Accountant')->first();
        $this->accountant = User::create([
            'name'      => 'Accountant',
            'email'     => 'accountant@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $role->id,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
    }

    public function test_accountant_dashboard_loads(): void
    {
        $this->actingAs($this->accountant)
            ->get('/accountant/dashboard')
            ->assertOk();
    }

    public function test_cashier_cannot_access_accountant_dashboard(): void
    {
        $cashierRole = Role::where('name', 'Cashier')->first();
        $cashier = User::create([
            'name'      => 'Cashier',
            'email'     => 'cashier@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $cashierRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($cashier)
            ->get('/accountant/dashboard')
            ->assertForbidden();
    }
}
