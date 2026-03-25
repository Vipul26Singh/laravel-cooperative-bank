<?php

namespace Tests\Feature\Manager;

use App\Models\{User, Role, Branch, Customer};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests the approval/rejection workflow using pre-created customers
 * (bypassing the Clerk store which has field mismatches with the DB).
 */
class CustomerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);

        $managerRole = Role::where('name', 'Manager')->first();
        $this->manager = User::create([
            'name' => 'Manager', 'email' => 'manager@coopbank.com',
            'password' => bcrypt('Password@123'), 'role_id' => $managerRole->id,
            'branch_id' => $this->branch->id, 'is_active' => true,
        ]);
    }

    public function test_approve_then_check_status(): void
    {
        $customer = Customer::create([
            'customer_number' => 1001, 'full_name' => 'Pending User',
            'gender' => 'Male', 'mobile' => '111', 'residential_address' => 'A',
            'branch_id' => $this->branch->id, 'approval_status' => 'pending',
            'is_member_active' => false, 'created_by' => $this->manager->id,
        ]);

        $this->actingAs($this->manager)
            ->post("/manager/customers/{$customer->id}/approve");

        $customer->refresh();
        $this->assertEquals('approved', $customer->approval_status);
        $this->assertTrue($customer->is_member_active);
        $this->assertEquals($this->manager->id, $customer->approved_by);
        $this->assertNotNull($customer->approval_date);
        $this->assertNotNull($customer->activation_date);
    }

    public function test_reject_then_check_status(): void
    {
        $customer = Customer::create([
            'customer_number' => 1002, 'full_name' => 'Rejected User',
            'gender' => 'Female', 'mobile' => '222', 'residential_address' => 'B',
            'branch_id' => $this->branch->id, 'approval_status' => 'pending',
            'is_member_active' => false, 'created_by' => $this->manager->id,
        ]);

        $this->actingAs($this->manager)
            ->post("/manager/customers/{$customer->id}/reject", [
                'rejection_reason' => 'Fake docs',
            ]);

        $customer->refresh();
        $this->assertEquals('rejected', $customer->approval_status);
        $this->assertFalse($customer->is_member_active);
        $this->assertEquals('Fake docs', $customer->approver_remark);
    }

    public function test_reject_without_reason_still_works(): void
    {
        $customer = Customer::create([
            'customer_number' => 1003, 'full_name' => 'No Reason',
            'gender' => 'Male', 'mobile' => '333', 'residential_address' => 'C',
            'branch_id' => $this->branch->id, 'approval_status' => 'pending',
            'is_member_active' => false, 'created_by' => $this->manager->id,
        ]);

        $this->actingAs($this->manager)
            ->post("/manager/customers/{$customer->id}/reject")
            ->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id, 'approval_status' => 'rejected',
        ]);
    }
}
