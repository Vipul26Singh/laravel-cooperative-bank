<?php

namespace Tests\Feature\Api;

use App\Models\{User, Role, Branch, Customer};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Manager')->first();
        $this->user = User::create([
            'name' => 'API User', 'email' => 'api@coopbank.com',
            'password' => bcrypt('Pass@123'), 'role_id' => $role->id,
            'branch_id' => $this->branch->id, 'is_active' => true,
        ]);
    }

    private function createCustomer(array $overrides = []): Customer
    {
        return Customer::create(array_merge([
            'customer_number' => 1001, 'full_name' => 'John Doe',
            'gender' => 'Male', 'mobile' => '9876543210',
            'residential_address' => '123 Test St',
            'branch_id' => $this->branch->id, 'approval_status' => 'pending',
            'is_member_active' => false, 'created_by' => $this->user->id,
        ], $overrides));
    }

    public function test_list_customers(): void
    {
        $this->createCustomer();
        $this->createCustomer(['customer_number' => 1002, 'full_name' => 'Jane Doe']);

        Sanctum::actingAs($this->user);
        $this->getJson('/api/customers')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_filter_customers_by_status(): void
    {
        $this->createCustomer(['approval_status' => 'pending']);
        $this->createCustomer(['customer_number' => 1002, 'approval_status' => 'approved', 'is_member_active' => true]);

        Sanctum::actingAs($this->user);
        $this->getJson('/api/customers?status=approved')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_create_customer(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/customers', [
            'full_name' => 'New Customer', 'gender' => 'Female',
            'mobile' => '9876543211', 'residential_address' => '456 Test Rd',
        ])->assertCreated()
            ->assertJsonFragment(['full_name' => 'New Customer', 'approval_status' => 'pending']);

        $this->assertDatabaseHas('customers', ['full_name' => 'New Customer', 'branch_id' => $this->branch->id]);
    }

    public function test_create_customer_validates_required(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/customers', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['full_name', 'gender', 'mobile', 'residential_address']);
    }

    public function test_show_customer(): void
    {
        $customer = $this->createCustomer();

        Sanctum::actingAs($this->user);
        $this->getJson("/api/customers/{$customer->id}")
            ->assertOk()
            ->assertJsonFragment(['full_name' => 'John Doe']);
    }

    public function test_update_customer(): void
    {
        $customer = $this->createCustomer();

        Sanctum::actingAs($this->user);
        $this->putJson("/api/customers/{$customer->id}", ['full_name' => 'Updated Name'])
            ->assertOk()
            ->assertJsonFragment(['full_name' => 'Updated Name']);
    }

    public function test_delete_customer(): void
    {
        $customer = $this->createCustomer();

        Sanctum::actingAs($this->user);
        $this->deleteJson("/api/customers/{$customer->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Customer deleted.']);

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_approve_customer(): void
    {
        $customer = $this->createCustomer();

        Sanctum::actingAs($this->user);
        $this->postJson("/api/customers/{$customer->id}/approve", ['remark' => 'All good'])
            ->assertOk()
            ->assertJsonFragment(['approval_status' => 'approved']);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id, 'approval_status' => 'approved', 'is_member_active' => true,
        ]);
    }

    public function test_reject_customer(): void
    {
        $customer = $this->createCustomer();

        Sanctum::actingAs($this->user);
        $this->postJson("/api/customers/{$customer->id}/reject", ['remark' => 'Fake docs'])
            ->assertOk()
            ->assertJsonFragment(['approval_status' => 'rejected']);
    }

    public function test_reject_requires_remark(): void
    {
        $customer = $this->createCustomer();

        Sanctum::actingAs($this->user);
        $this->postJson("/api/customers/{$customer->id}/reject", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('remark');
    }
}
