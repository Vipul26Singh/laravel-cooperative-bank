<?php

namespace Tests\Browser;

use App\Models\{User, Role, Branch};
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T05_CashierFlowTest extends DuskTestCase
{
    public function test_full_cashier_flow(): void
    {
        $branch = Branch::firstOrCreate(['code' => 'BRCSH'], ['name' => 'Cashier Branch', 'address' => 'Cash St', 'is_active' => true]);
        $role = Role::where('name', 'Cashier')->first();
        User::firstOrCreate(['email' => 'csh@coopbank.com'], [
            'name' => 'Dusk Cashier', 'password' => bcrypt('Cashier@123'),
            'role_id' => $role->id, 'branch_id' => $branch->id, 'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {

            // ── Login ────────────────────────────────────────────
            $browser->visit('/login')
                ->type('email', 'csh@coopbank.com')
                ->type('password', 'Cashier@123')
                ->press('Sign In')
                ->waitForText('Cashier Dashboard', 15);

            // ── Dashboard ────────────────────────────────────────
            $browser->assertSee('Cashier Dashboard')
                ->screenshot('05-cashier/70-cashier-dashboard');

            // ── Transactions Index ───────────────────────────────
            $browser->visit('/cashier/transactions')
                ->waitForText('Transaction List')
                ->screenshot('05-cashier/71-cashier-transactions');

            // ── Transaction Create Form ──────────────────────────
            $browser->visit('/cashier/transactions/create')
                ->waitForText('New Bank Transaction')
                ->assertSee('Account Number')
                ->assertSee('Transaction Type')
                ->assertSee('Amount')
                ->screenshot('05-cashier/72-cashier-transaction-form');

            // ── Loan Repayments Index ────────────────────────────
            $browser->visit('/cashier/loan-repayments')
                ->waitForText('Repayments')
                ->screenshot('05-cashier/73-cashier-loan-repayments');

            // ── Loan Repayment Create Form ───────────────────────
            $browser->visit('/cashier/loan-repayments/create')
                ->waitForText('Loan Repayment')
                ->assertSee('Search')
                ->screenshot('05-cashier/74-cashier-loan-repayment-create');

            // ── Logout ───────────────────────────────────────────
            $browser->visit('/cashier/dashboard')
                ->waitForText('Cashier Dashboard')
                ->click('a[data-toggle="dropdown"]')
                ->pause(500)
                ->press('Logout')
                ->waitForLocation('/login')
                ->screenshot('05-cashier/75-cashier-logged-out');
        });
    }
}
