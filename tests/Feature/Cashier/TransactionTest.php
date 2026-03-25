<?php

namespace Tests\Feature\Cashier;

use App\Models\{User, Role, Branch, Customer, AccountType, BankAccount, InitializeAccountNumber};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;
    private Branch $branch;
    private BankAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Cashier')->first();
        $this->cashier = User::create([
            'name'      => 'Cashier',
            'email'     => 'cashier@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $role->id,
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'customer_number' => 1001, 'full_name' => 'John Doe', 'gender' => 'Male',
            'mobile' => '9876543210', 'residential_address' => 'Addr',
            'branch_id' => $this->branch->id, 'approval_status' => 'approved',
            'is_member_active' => true, 'created_by' => $this->cashier->id,
        ]);

        $accountType = AccountType::create([
            'name' => 'Savings', 'type' => 'Savings', 'is_active' => true,
        ]);

        $this->account = BankAccount::create([
            'account_number'  => 100001,
            'customer_id'     => $customer->id,
            'account_type_id' => $accountType->id,
            'branch_id'       => $this->branch->id,
            'balance'         => 10000.00,
            'opening_date'    => now(),
            'is_active'       => true,
            'created_by'      => $this->cashier->id,
        ]);
    }

    public function test_transaction_create_page_loads(): void
    {
        $this->actingAs($this->cashier)
            ->get('/cashier/transactions/create')
            ->assertOk();
    }

    public function test_validates_required_fields(): void
    {
        $this->actingAs($this->cashier)
            ->post('/cashier/transactions', [])
            ->assertSessionHasErrors(['bank_account_id', 'transaction_type', 'amount', 'transaction_mode']);
    }

    public function test_validates_transaction_type_enum(): void
    {
        $this->actingAs($this->cashier)
            ->post('/cashier/transactions', [
                'bank_account_id'  => $this->account->id,
                'transaction_type' => 'Invalid',
                'amount'           => 1000,
                'transaction_mode' => 'cash',
            ])
            ->assertSessionHasErrors('transaction_type');
    }

    public function test_validates_transaction_mode_enum(): void
    {
        $this->actingAs($this->cashier)
            ->post('/cashier/transactions', [
                'bank_account_id'  => $this->account->id,
                'transaction_type' => 'Deposit',
                'amount'           => 1000,
                'transaction_mode' => 'bitcoin',
            ])
            ->assertSessionHasErrors('transaction_mode');
    }

    public function test_validates_minimum_amount(): void
    {
        $this->actingAs($this->cashier)
            ->post('/cashier/transactions', [
                'bank_account_id'  => $this->account->id,
                'transaction_type' => 'Deposit',
                'amount'           => 0,
                'transaction_mode' => 'cash',
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_cheque_fields_required_for_cheque_mode(): void
    {
        $this->actingAs($this->cashier)
            ->post('/cashier/transactions', [
                'bank_account_id'  => $this->account->id,
                'transaction_type' => 'Deposit',
                'amount'           => 1000,
                'transaction_mode' => 'cheque',
            ])
            ->assertSessionHasErrors(['cheque_number', 'bank_name', 'cheque_date']);
    }

    public function test_cheque_fields_not_required_for_cash_mode(): void
    {
        $this->actingAs($this->cashier)
            ->post('/cashier/transactions', [
                'bank_account_id'  => $this->account->id,
                'transaction_type' => 'Deposit',
                'amount'           => 1000,
                'transaction_mode' => 'cash',
            ])
            ->assertSessionDoesntHaveErrors(['cheque_number', 'bank_name', 'cheque_date']);
    }

    public function test_deposit_updates_balance(): void
    {
        $response = $this->actingAs($this->cashier)
            ->post('/cashier/transactions', [
                'bank_account_id'  => $this->account->id,
                'account_number'   => $this->account->account_number,
                'transaction_type' => 'Deposit',
                'amount'           => 5000,
                'transaction_mode' => 'cash',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->account->refresh();
        $this->assertEquals(15000.00, $this->account->balance);

        $this->assertDatabaseHas('bank_account_transactions', [
            'bank_account_id'  => $this->account->id,
            'transaction_type' => 'Deposit',
            'amount'           => 5000,
            'balance_after'    => 15000,
        ]);
    }

    public function test_withdrawal_updates_balance(): void
    {
        $response = $this->actingAs($this->cashier)
            ->post('/cashier/transactions', [
                'bank_account_id'  => $this->account->id,
                'account_number'   => $this->account->account_number,
                'transaction_type' => 'Withdraw',
                'amount'           => 3000,
                'transaction_mode' => 'cash',
            ]);

        $response->assertRedirect();

        $this->account->refresh();
        $this->assertEquals(7000.00, $this->account->balance);
    }

    public function test_withdrawal_fails_on_insufficient_balance(): void
    {
        $response = $this->actingAs($this->cashier)
            ->post('/cashier/transactions', [
                'bank_account_id'  => $this->account->id,
                'account_number'   => $this->account->account_number,
                'transaction_type' => 'Withdraw',
                'amount'           => 99999,
                'transaction_mode' => 'cash',
            ]);

        // Should get a 500 error (insufficient balance exception)
        $response->assertStatus(500);

        // Balance should remain unchanged
        $this->account->refresh();
        $this->assertEquals(10000.00, $this->account->balance);
    }
}
