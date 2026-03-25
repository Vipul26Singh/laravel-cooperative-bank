<?php

namespace Tests\Feature\Clerk;

use App\Models\{User, Role, Branch, Customer, LoanType, LoanApplication};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    private User $clerk;
    private Branch $branch;
    private Customer $customer;
    private LoanType $loanType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Clerk')->first();
        $this->clerk = User::create([
            'name' => 'Clerk', 'email' => 'clerk@coopbank.com',
            'password' => bcrypt('Password@123'), 'role_id' => $role->id,
            'branch_id' => $this->branch->id, 'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'customer_number' => 1001, 'full_name' => 'John Doe',
            'gender' => 'Male', 'mobile' => '9876543210', 'residential_address' => 'Test Addr',
            'branch_id' => $this->branch->id, 'approval_status' => 'approved',
            'is_member_active' => true, 'created_by' => $this->clerk->id,
        ]);

        $this->loanType = LoanType::create([
            'name' => 'Personal Loan', 'interest_rate' => 12,
            'duration_months' => 60, 'max_amount' => 500000,
            'num_installments' => 60, 'is_active' => true,
        ]);
    }

    public function test_loan_application_validates_required_fields(): void
    {
        $this->actingAs($this->clerk)
            ->post('/clerk/loan-applications', [])
            ->assertSessionHasErrors(['customer_id', 'loan_type_id', 'applied_amount', 'duration_months', 'loan_purpose', 'frequency']);
    }

    public function test_loan_application_validates_minimum_amount(): void
    {
        $this->actingAs($this->clerk)
            ->post('/clerk/loan-applications', [
                'customer_id' => $this->customer->id, 'loan_type_id' => $this->loanType->id,
                'applied_amount' => 500, 'duration_months' => 12,
                'loan_purpose' => 'Test', 'frequency' => 'MONTHLY',
            ])
            ->assertSessionHasErrors('applied_amount');
    }

    public function test_loan_application_validates_frequency(): void
    {
        $this->actingAs($this->clerk)
            ->post('/clerk/loan-applications', [
                'customer_id' => $this->customer->id, 'loan_type_id' => $this->loanType->id,
                'applied_amount' => 50000, 'duration_months' => 12,
                'loan_purpose' => 'Test', 'frequency' => 'INVALID',
            ])
            ->assertSessionHasErrors('frequency');
    }

    public function test_loan_application_submit_creates_record(): void
    {
        $response = $this->actingAs($this->clerk)
            ->post('/clerk/loan-applications', [
                'customer_id' => $this->customer->id, 'loan_type_id' => $this->loanType->id,
                'applied_amount' => 100000, 'duration_months' => 24,
                'loan_purpose' => 'Home renovation', 'frequency' => 'MONTHLY',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'customer_id'     => $this->customer->id,
            'applied_amount'  => 100000,
            'loan_purpose'    => 'Home renovation',
            'approval_status' => 'pending',
            'branch_id'       => $this->branch->id,
        ]);
    }

    public function test_loan_application_sets_branch_and_creator(): void
    {
        $this->actingAs($this->clerk)
            ->post('/clerk/loan-applications', [
                'customer_id' => $this->customer->id, 'loan_type_id' => $this->loanType->id,
                'applied_amount' => 50000, 'duration_months' => 12,
                'loan_purpose' => 'Education', 'frequency' => 'MONTHLY',
            ]);

        $app = LoanApplication::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($app);
        $this->assertEquals($this->branch->id, $app->branch_id);
        $this->assertEquals($this->clerk->id, $app->created_by);
    }
}
