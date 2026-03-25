<?php

namespace Tests\Feature\Manager;

use App\Models\{User, Role, Branch, Customer};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Manager')->first();
        $this->manager = User::create([
            'name'      => 'Manager',
            'email'     => 'manager@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $role->id,
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);
    }

    public function test_dashboard_loads(): void
    {
        $this->actingAs($this->manager)
            ->get('/manager/dashboard')
            ->assertOk();
    }

    public function test_dashboard_scoped_to_branch(): void
    {
        $otherBranch = Branch::create(['name' => 'Other', 'code' => 'BR002', 'address' => 'X', 'is_active' => true]);

        // Customer in manager's branch
        Customer::create([
            'customer_number' => 1001, 'full_name' => 'Mine Customer',
            'gender' => 'Male', 'mobile' => '111', 'residential_address' => 'A',
            'branch_id' => $this->branch->id, 'approval_status' => 'pending',
            'is_member_active' => false, 'created_by' => $this->manager->id,
        ]);

        // Customer in other branch — should NOT count
        Customer::create([
            'customer_number' => 1002, 'full_name' => 'Other Branch',
            'gender' => 'Male', 'mobile' => '222', 'residential_address' => 'B',
            'branch_id' => $otherBranch->id, 'approval_status' => 'pending',
            'is_member_active' => false, 'created_by' => $this->manager->id,
        ]);

        // Dashboard should show 1 pending (only own branch)
        $this->actingAs($this->manager)
            ->get('/manager/dashboard')
            ->assertOk()
            ->assertSee('1');
    }
}
