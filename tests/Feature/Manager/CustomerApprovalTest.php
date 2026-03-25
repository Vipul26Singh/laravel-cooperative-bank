<?php

namespace Tests\Feature\Manager;

use App\Models\{User, Role, Branch, Customer};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApprovalTest extends TestCase
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

    private function createPendingCustomer(): Customer
    {
        return Customer::create([
            'customer_number'     => 1001,
            'full_name'           => 'John Doe',
            'gender'              => 'Male',
            'mobile'              => '9876543210',
            'residential_address' => '123 Test St',
            'branch_id'        => $this->branch->id,
            'approval_status'  => 'pending',
            'is_member_active' => false,
            'created_by'       => $this->manager->id,
        ]);
    }

    public function test_manager_dashboard_loads(): void
    {
        $this->actingAs($this->manager)
            ->get('/manager/dashboard')
            ->assertOk();
    }

    public function test_manager_can_approve_customer(): void
    {
        $customer = $this->createPendingCustomer();

        $this->actingAs($this->manager)
            ->post("/manager/customers/{$customer->id}/approve")
            ->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'id'               => $customer->id,
            'approval_status'  => 'approved',
            'is_member_active' => true,
        ]);
    }

    public function test_manager_can_reject_customer(): void
    {
        $customer = $this->createPendingCustomer();

        $this->actingAs($this->manager)
            ->post("/manager/customers/{$customer->id}/reject", [
                'rejection_reason' => 'Incomplete documents',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'id'              => $customer->id,
            'approval_status' => 'rejected',
            'approver_remark' => 'Incomplete documents',
        ]);
    }

    public function test_approval_sets_metadata(): void
    {
        $customer = $this->createPendingCustomer();

        $this->actingAs($this->manager)
            ->post("/manager/customers/{$customer->id}/approve");

        $customer->refresh();
        $this->assertEquals($this->manager->id, $customer->approved_by);
        $this->assertNotNull($customer->approval_date);
    }

    public function test_clerk_cannot_approve_customer(): void
    {
        $clerkRole = Role::where('name', 'Clerk')->first();
        $clerk = User::create([
            'name' => 'Clerk', 'email' => 'clerk@coopbank.com',
            'password' => bcrypt('Password@123'), 'role_id' => $clerkRole->id,
            'branch_id' => $this->branch->id, 'is_active' => true,
        ]);

        $customer = $this->createPendingCustomer();

        $this->actingAs($clerk)
            ->post("/manager/customers/{$customer->id}/approve")
            ->assertForbidden();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id, 'approval_status' => 'pending',
        ]);
    }

    public function test_cashier_cannot_approve_customer(): void
    {
        $cashierRole = Role::where('name', 'Cashier')->first();
        $cashier = User::create([
            'name' => 'Cashier', 'email' => 'cashier@coopbank.com',
            'password' => bcrypt('Password@123'), 'role_id' => $cashierRole->id,
            'branch_id' => $this->branch->id, 'is_active' => true,
        ]);

        $customer = $this->createPendingCustomer();

        $this->actingAs($cashier)
            ->post("/manager/customers/{$customer->id}/approve")
            ->assertForbidden();
    }
}
