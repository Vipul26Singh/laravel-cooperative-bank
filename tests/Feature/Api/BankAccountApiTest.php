<?php

namespace Tests\Feature\Api;

use App\Models\{User, Role, Branch, Customer, AccountType, BankAccount, InitializeAccountNumber};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BankAccountApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Branch $branch;
    private Customer $customer;
    private AccountType $accountType;

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

        $this->accountType = AccountType::create(['name' => 'Savings', 'type' => 'Savings', 'is_active' => true]);

        InitializeAccountNumber::create([
            'branch_id' => $this->branch->id, 'bank_account_start' => 100001,
            'loan_account_start' => 200001, 'fd_account_start' => 300001,
        ]);
    }

    public function test_open_account(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/bank-accounts', [
            'customer_id' => $this->customer->id,
            'account_type_id' => $this->accountType->id,
            'opening_date' => '2026-01-01',
            'balance' => 5000,
        ])->assertCreated()
            ->assertJsonFragment(['account_number' => 100001]);

        $this->assertDatabaseHas('bank_accounts', [
            'customer_id' => $this->customer->id, 'account_number' => 100001,
        ]);
    }

    public function test_list_accounts(): void
    {
        BankAccount::create([
            'account_number' => 100001, 'customer_id' => $this->customer->id,
            'account_type_id' => $this->accountType->id, 'branch_id' => $this->branch->id,
            'balance' => 5000, 'opening_date' => now(), 'is_active' => true,
            'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->getJson('/api/bank-accounts')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_show_account(): void
    {
        $account = BankAccount::create([
            'account_number' => 100001, 'customer_id' => $this->customer->id,
            'account_type_id' => $this->accountType->id, 'branch_id' => $this->branch->id,
            'balance' => 5000, 'opening_date' => now(), 'is_active' => true,
            'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->getJson("/api/bank-accounts/{$account->id}")
            ->assertOk()
            ->assertJsonFragment(['account_number' => 100001]);
    }

    public function test_search_by_account_number(): void
    {
        BankAccount::create([
            'account_number' => 100001, 'customer_id' => $this->customer->id,
            'account_type_id' => $this->accountType->id, 'branch_id' => $this->branch->id,
            'balance' => 5000, 'opening_date' => now(), 'is_active' => true,
            'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->getJson('/api/bank-accounts/search/100001')
            ->assertOk()
            ->assertJsonFragment(['account_number' => 100001]);
    }

    public function test_search_nonexistent_account(): void
    {
        Sanctum::actingAs($this->user);
        $this->getJson('/api/bank-accounts/search/999999')
            ->assertNotFound();
    }

    public function test_open_account_validates_required(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/bank-accounts', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['customer_id', 'account_type_id', 'opening_date']);
    }

    public function test_close_account(): void
    {
        $account = BankAccount::create([
            'account_number' => 100001, 'customer_id' => $this->customer->id,
            'account_type_id' => $this->accountType->id, 'branch_id' => $this->branch->id,
            'balance' => 0, 'opening_date' => now(), 'is_active' => true,
            'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
        $this->deleteJson("/api/bank-accounts/{$account->id}")
            ->assertOk();

        $this->assertSoftDeleted('bank_accounts', ['id' => $account->id]);
    }
}
