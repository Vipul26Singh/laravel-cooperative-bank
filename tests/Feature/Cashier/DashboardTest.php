<?php

namespace Tests\Feature\Cashier;

use App\Models\{User, Role, Branch};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Cashier')->first();
        $this->cashier = User::create([
            'name'      => 'Cashier',
            'email'     => 'cashier@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $role->id,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
    }

    public function test_cashier_dashboard_loads(): void
    {
        $this->actingAs($this->cashier)
            ->get('/cashier/dashboard')
            ->assertOk();
    }

    public function test_transaction_create_form_loads(): void
    {
        $this->actingAs($this->cashier)
            ->get('/cashier/transactions/create')
            ->assertOk();
    }
}
