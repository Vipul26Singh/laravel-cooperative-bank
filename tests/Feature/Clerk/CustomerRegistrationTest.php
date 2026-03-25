<?php

namespace Tests\Feature\Clerk;

use App\Models\{User, Role, Branch, Customer};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $clerk;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Clerk')->first();
        $this->clerk = User::create([
            'name'      => 'Clerk',
            'email'     => 'clerk@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $role->id,
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);
    }

    public function test_clerk_dashboard_loads(): void
    {
        $this->actingAs($this->clerk)
            ->get('/clerk/dashboard')
            ->assertOk();
    }

    public function test_customer_index_loads(): void
    {
        $this->actingAs($this->clerk)
            ->get('/clerk/customers')
            ->assertOk();
    }

    public function test_customer_create_form_loads(): void
    {
        $this->actingAs($this->clerk)
            ->get('/clerk/customers/create')
            ->assertOk();
    }

    public function test_register_validates_required_fields(): void
    {
        $this->actingAs($this->clerk)
            ->post('/clerk/customers', [])
            ->assertSessionHasErrors(['full_name', 'gender', 'mobile', 'residential_address']);
    }

    public function test_register_validates_gender_enum(): void
    {
        $this->actingAs($this->clerk)
            ->post('/clerk/customers', [
                'full_name'           => 'Test',
                'gender'              => 'InvalidGender',
                'mobile'              => '9876543210',
                'residential_address' => 'Addr',
            ])
            ->assertSessionHasErrors('gender');
    }

    public function test_register_accepts_valid_genders(): void
    {
        foreach (['Male', 'Female', 'Other'] as $gender) {
            $this->actingAs($this->clerk)
                ->post('/clerk/customers', [
                    'full_name'           => "Test $gender",
                    'gender'              => $gender,
                    'mobile'              => '9876543210',
                    'residential_address' => 'Addr',
                ])
                ->assertSessionDoesntHaveErrors('gender');
        }
    }

    public function test_customer_list_only_shows_own_customers(): void
    {
        Customer::create([
            'customer_number' => 1001, 'full_name' => 'My Customer',
            'gender' => 'Male', 'mobile' => '111', 'residential_address' => 'Addr',
            'branch_id' => $this->branch->id, 'approval_status' => 'pending',
            'is_member_active' => false, 'created_by' => $this->clerk->id,
        ]);

        $otherClerk = User::create([
            'name' => 'Other', 'email' => 'other@coopbank.com',
            'password' => bcrypt('Password@123'), 'role_id' => $this->clerk->role_id,
            'branch_id' => $this->branch->id, 'is_active' => true,
        ]);
        Customer::create([
            'customer_number' => 1002, 'full_name' => 'Not Mine',
            'gender' => 'Female', 'mobile' => '222', 'residential_address' => 'Addr',
            'branch_id' => $this->branch->id, 'approval_status' => 'pending',
            'is_member_active' => false, 'created_by' => $otherClerk->id,
        ]);

        $response = $this->actingAs($this->clerk)->get('/clerk/customers');
        $response->assertSee('My');
        $response->assertDontSee('Not Mine');
    }
}
