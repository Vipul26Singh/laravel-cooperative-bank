<?php

namespace Tests\Feature\Api;

use App\Models\{User, Role, Branch, Customer, LoanType, Loan, InitializeAccountNumber};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Branch $branch;
    private Customer $customer;
    private LoanType $loanType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Manager')->first();
        $this->user = User::create([
            'name' => 'Manager', 'email' => 'mgr@coopbank.com',
            'password' => bcrypt('Pass@123'), 'role_id' => $role->id,
            'branch_id' => $this->branch->id, 'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'customer_number' => 1001, 'full_name' => 'John Doe', 'gender' => 'Male',
            'mobile' => '9876543210', 'residential_address' => 'Addr',
            'branch_id' => $this->branch->id, 'approval_status' => 'approved',
            'is_member_active' => true, 'created_by' => $this->user->id,
        ]);

        $this->loanType = LoanType::create([
            'name' => 'Personal Loan', 'interest_rate' => 12,
            'duration_months' => 60, 'max_amount' => 500000,
            'num_installments' => 60, 'is_active' => true,
        ]);

        InitializeAccountNumber::create([
            'branch_id' => $this->branch->id, 'bank_account_start' => 100001,
            'loan_account_start' => 200001, 'fd_account_start' => 300001,
        ]);
    }

    public function test_disburse_loan(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/loans', [
            'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id,
            'amount' => 100000,
            'interest_rate' => 12,
            'duration_months' => 24,
            'frequency' => 'MONTHLY',
            'first_installment_date' => '2026-05-01',
        ])->assertCreated()
            ->assertJsonFragment(['status' => 'active', 'loan_number' => 200001]);

        $this->assertDatabaseHas('loans', [
            'customer_id' => $this->customer->id, 'amount' => 100000, 'status' => 'active',
        ]);
    }

    public function test_disburse_validates_required(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/loans', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['customer_id', 'loan_type_id', 'amount', 'interest_rate', 'duration_months', 'frequency', 'first_installment_date']);
    }

    public function test_list_loans(): void
    {
        Loan::create([
            'loan_number' => 200001, 'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id, 'branch_id' => $this->branch->id,
            'amount' => 100000, 'interest_rate' => 12, 'duration_months' => 24,
            'num_installments' => 24, 'installment_amount' => 4707.35,
            'outstanding_balance' => 100000, 'status' => 'active',
            'loan_date' => now(), 'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->getJson('/api/loans')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_filter_loans_by_status(): void
    {
        Loan::create([
            'loan_number' => 200001, 'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id, 'branch_id' => $this->branch->id,
            'amount' => 100000, 'interest_rate' => 12, 'duration_months' => 24,
            'num_installments' => 24, 'installment_amount' => 4707.35,
            'outstanding_balance' => 100000, 'status' => 'active',
            'loan_date' => now(), 'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->getJson('/api/loans?status=closed')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_show_loan(): void
    {
        $loan = Loan::create([
            'loan_number' => 200001, 'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id, 'branch_id' => $this->branch->id,
            'amount' => 100000, 'interest_rate' => 12, 'duration_months' => 24,
            'num_installments' => 24, 'installment_amount' => 4707.35,
            'outstanding_balance' => 100000, 'status' => 'active',
            'loan_date' => now(), 'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->getJson("/api/loans/{$loan->id}")
            ->assertOk()
            ->assertJsonFragment(['loan_number' => 200001]);
    }

    public function test_installment_schedule(): void
    {
        $loan = Loan::create([
            'loan_number' => 200001, 'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id, 'branch_id' => $this->branch->id,
            'amount' => 100000, 'interest_rate' => 12, 'duration_months' => 12,
            'num_installments' => 12, 'installment_amount' => 8884.88,
            'outstanding_balance' => 100000, 'status' => 'active',
            'frequency' => 'MONTHLY', 'loan_date' => now(), 'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->getJson("/api/loans/{$loan->id}/schedule")
            ->assertOk()
            ->assertJsonStructure(['loan', 'schedule']);

        $this->assertCount(12, $response->json('schedule'));
    }

    public function test_record_repayment(): void
    {
        $loan = Loan::create([
            'loan_number' => 200001, 'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id, 'branch_id' => $this->branch->id,
            'amount' => 100000, 'interest_rate' => 12, 'duration_months' => 12,
            'num_installments' => 12, 'installment_amount' => 8884.88,
            'outstanding_balance' => 100000, 'status' => 'active',
            'loan_date' => now(), 'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->postJson("/api/loans/{$loan->id}/repayment", [
            'amount_paid' => 8884.88, 'principal_paid' => 7884.88,
            'interest_paid' => 1000, 'payment_date' => '2026-04-01',
            'transaction_mode' => 'cash',
        ])->assertCreated();

        $loan->refresh();
        $this->assertEquals(92115.12, round($loan->outstanding_balance, 2));
    }
}
