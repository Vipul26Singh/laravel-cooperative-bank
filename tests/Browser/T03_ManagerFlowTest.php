<?php

namespace Tests\Browser;

use App\Models\{User, Role, Branch};
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T03_ManagerFlowTest extends DuskTestCase
{
    public function test_full_manager_flow(): void
    {
        $branch = Branch::firstOrCreate(['code' => 'BRMGR'], ['name' => 'Manager Branch', 'address' => 'Mgr St', 'is_active' => true]);
        $role = Role::where('name', 'Manager')->first();
        User::firstOrCreate(['email' => 'mgr@coopbank.com'], [
            'name' => 'Dusk Manager', 'password' => bcrypt('Manager@123'),
            'role_id' => $role->id, 'branch_id' => $branch->id, 'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {

            // ── Login ────────────────────────────────────────────
            $browser->visit('/login')
                ->type('email', 'mgr@coopbank.com')
                ->type('password', 'Manager@123')
                ->press('Sign In')
                ->waitForText('Manager Dashboard', 15);

            // ── Dashboard ────────────────────────────────────────
            $browser->assertSee('Pending Approvals')
                ->assertSee('Bank Accounts')
                ->assertSee('Quick Links')
                ->screenshot('03-manager/50-manager-dashboard');

            // ── All sidebar pages ────────────────────────────────
            $browser->visit('/manager/customers')
                ->waitForText('Branch Customers')
                ->screenshot('03-manager/51-manager-customers');

            $browser->visit('/manager/bank-accounts')
                ->waitForText('Branch Accounts')
                ->screenshot('03-manager/52-manager-bank-accounts');

            $browser->visit('/manager/bank-accounts/create')
                ->waitForText('New Account')
                ->screenshot('03-manager/53-manager-bank-account-create');

            $browser->visit('/manager/fd-accounts')
                ->waitForText('Fixed Deposits')
                ->screenshot('03-manager/54-manager-fd-accounts');

            $browser->visit('/manager/fd-accounts/create')
                ->waitForText('New FD')
                ->screenshot('03-manager/55-manager-fd-account-create');

            $browser->visit('/manager/loans')
                ->waitForText('Branch Loans')
                ->screenshot('03-manager/56-manager-loans');

            $browser->visit('/manager/loans/create')
                ->waitForText('Loan Disbursement')
                ->screenshot('03-manager/57-manager-loan-create');

            $browser->visit('/manager/loan-applications')
                ->waitForText('Applications')
                ->screenshot('03-manager/58-manager-loan-applications');

            // ── Logout ───────────────────────────────────────────
            $browser->visit('/manager/dashboard')
                ->waitForText('Manager Dashboard')
                ->click('a[data-toggle="dropdown"]')
                ->pause(500)
                ->press('Logout')
                ->waitForLocation('/login')
                ->screenshot('03-manager/59-manager-logged-out');
        });
    }
}
