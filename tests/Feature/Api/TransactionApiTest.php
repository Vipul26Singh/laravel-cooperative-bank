<?php

namespace Tests\Feature\Api;

use App\Models\{User, Role, Branch, Customer, AccountType, BankAccount};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Branch $branch;
    private BankAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Cashier')->first();
        $this->user = User::create([
            'name' => 'Cashier', 'email' => 'cashier@coopbank.com',
            'password' => bcrypt('Pass@123'), 'role_id' => $role->id,
            'branch_id' => $this->branch->id, 'is_active' => true,
        ]);

        $customer = Customer::create([
            'customer_number' => 1001, 'full_name' => 'John Doe', 'gender' => 'Male',
            'mobile' => '9876543210', 'residential_address' => 'Addr',
            'branch_id' => $this->branch->id, 'approval_status' => 'approved',
            'is_member_active' => true, 'created_by' => $this->user->id,
        ]);

        $accountType = AccountType::create(['name' => 'Savings', 'type' => 'Savings', 'is_active' => true]);

        $this->account = BankAccount::create([
            'account_number' => 100001, 'customer_id' => $customer->id,
            'account_type_id' => $accountType->id, 'branch_id' => $this->branch->id,
            'balance' => 10000.00, 'opening_date' => now(), 'is_active' => true,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_deposit_via_api(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/transactions', [
            'account_number'   => 100001,
            'transaction_type' => 'Deposit',
            'amount'           => 5000,
            'transaction_mode' => 'cash',
        ])->assertCreated()
            ->assertJsonFragment(['transaction_type' => 'Deposit']);

        $this->account->refresh();
        $this->assertEquals(15000.00, $this->account->balance);
    }

    public function test_withdraw_via_api(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/transactions', [
            'account_number'   => 100001,
            'transaction_type' => 'Withdraw',
            'amount'           => 3000,
            'transaction_mode' => 'cash',
        ])->assertCreated();

        $this->account->refresh();
        $this->assertEquals(7000.00, $this->account->balance);
    }

    public function test_withdraw_fails_on_insufficient_balance(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/transactions', [
            'account_number'   => 100001,
            'transaction_type' => 'Withdraw',
            'amount'           => 99999,
            'transaction_mode' => 'cash',
        ])->assertStatus(500);

        $this->account->refresh();
        $this->assertEquals(10000.00, $this->account->balance);
    }

    public function test_transaction_validates_required(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/transactions', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['account_number', 'transaction_type', 'amount', 'transaction_mode']);
    }

    public function test_transaction_validates_type_enum(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/transactions', [
            'account_number' => 100001, 'transaction_type' => 'Transfer',
            'amount' => 100, 'transaction_mode' => 'cash',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('transaction_type');
    }

    public function test_cheque_fields_required_for_cheque_mode(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/transactions', [
            'account_number' => 100001, 'transaction_type' => 'Deposit',
            'amount' => 1000, 'transaction_mode' => 'cheque',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['cheque_number', 'bank_name', 'cheque_date']);
    }

    public function test_list_transactions(): void
    {
        Sanctum::actingAs($this->user);

        // Create a transaction first
        $this->postJson('/api/transactions', [
            'account_number' => 100001, 'transaction_type' => 'Deposit',
            'amount' => 1000, 'transaction_mode' => 'cash',
        ]);

        $this->getJson('/api/transactions')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_transaction_fails_for_nonexistent_account(): void
    {
        Sanctum::actingAs($this->user);
        $this->postJson('/api/transactions', [
            'account_number' => 999999, 'transaction_type' => 'Deposit',
            'amount' => 100, 'transaction_mode' => 'cash',
        ])->assertNotFound();
    }
}
