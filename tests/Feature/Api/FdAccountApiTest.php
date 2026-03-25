<?php

namespace Tests\Feature\Api;

use App\Models\{User, Role, Branch, Customer, FdSetup, FdAccount, InitializeAccountNumber};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FdAccountApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Branch $branch;
    private Customer $customer;
    private FdSetup $fdSetup;

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

        $this->fdSetup = FdSetup::create([
            'description' => '1 Year FD', 'interest_rate' => 7.5,
            'duration_days' => 365, 'is_active' => true,
        ]);

        InitializeAccountNumber::create([
            'branch_id' => $this->branch->id, 'bank_account_start' => 100001,
            'loan_account_start' => 200001, 'fd_account_start' => 300001,
        ]);
    }

    public function test_open_fd(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/fd-accounts', [
            'customer_id' => $this->customer->id,
            'fd_setup_id' => $this->fdSetup->id,
            'principal_amount' => 50000,
            'fd_date' => '2026-01-01',
            'transaction_mode' => 'cash',
        ])->assertCreated()
            ->assertJsonFragment(['fd_number' => 300001]);

        $this->assertDatabaseHas('fd_accounts', [
            'customer_id' => $this->customer->id, 'principal_amount' => 50000,
        ]);
    }

    public function test_fd_validates_required(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/fd-accounts', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['customer_id', 'fd_setup_id', 'principal_amount', 'fd_date', 'transaction_mode']);
    }

    public function test_fd_validates_minimum_amount(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/fd-accounts', [
            'customer_id' => $this->customer->id, 'fd_setup_id' => $this->fdSetup->id,
            'principal_amount' => 500, 'fd_date' => '2026-01-01', 'transaction_mode' => 'cash',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('principal_amount');
    }

    public function test_list_fd_accounts(): void
    {
        FdAccount::create([
            'fd_number' => 300001, 'customer_id' => $this->customer->id,
            'fd_setup_id' => $this->fdSetup->id, 'branch_id' => $this->branch->id,
            'principal_amount' => 50000, 'interest_rate' => 7.5, 'duration_days' => 365,
            'fd_date' => now(), 'maturity_amount' => 53750, 'maturity_date' => now()->addDays(365),
            'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->getJson('/api/fd-accounts')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_show_fd_account(): void
    {
        $fd = FdAccount::create([
            'fd_number' => 300001, 'customer_id' => $this->customer->id,
            'fd_setup_id' => $this->fdSetup->id, 'branch_id' => $this->branch->id,
            'principal_amount' => 50000, 'interest_rate' => 7.5, 'duration_days' => 365,
            'fd_date' => now(), 'maturity_amount' => 53750, 'maturity_date' => now()->addDays(365),
            'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->getJson("/api/fd-accounts/{$fd->id}")
            ->assertOk()
            ->assertJsonFragment(['fd_number' => 300001]);
    }
}
