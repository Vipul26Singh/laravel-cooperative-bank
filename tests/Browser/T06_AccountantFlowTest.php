<?php

namespace Tests\Browser;

use App\Models\{User, Role, Branch};
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T06_AccountantFlowTest extends DuskTestCase
{
    public function test_full_accountant_flow(): void
    {
        $branch = Branch::firstOrCreate(['code' => 'BRACC'], ['name' => 'Accountant Branch', 'address' => 'Acc St', 'is_active' => true]);
        $role = Role::where('name', 'Accountant')->first();
        User::firstOrCreate(['email' => 'acc@coopbank.com'], [
            'name' => 'Dusk Accountant', 'password' => bcrypt('Accountant@123'),
            'role_id' => $role->id, 'branch_id' => $branch->id, 'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {

            // ── Login ────────────────────────────────────────────
            $browser->visit('/login')
                ->type('email', 'acc@coopbank.com')
                ->type('password', 'Accountant@123')
                ->press('Sign In')
                ->waitForText('Accountant Dashboard', 15);

            // ── Dashboard ────────────────────────────────────────
            $browser->assertSee('Accountant Dashboard')
                ->screenshot('06-accountant/80-accountant-dashboard');

            // ── Loan Outstanding Report ──────────────────────────
            $browser->visit('/accountant/reports/loan-outstanding')
                ->waitForText('Loan Outstanding Report')
                ->screenshot('06-accountant/81-accountant-loan-outstanding');

            // ── Transaction Statement ────────────────────────────
            $today = date('Y-m-d');
            $browser->visit("/accountant/reports/transaction-statement?from_date={$today}&to_date={$today}")
                ->waitForText('Transaction Statement')
                ->screenshot('06-accountant/82-accountant-transaction-statement');

            // ── Loan Demand ──────────────────────────────────────
            $browser->visit('/accountant/reports/loan-demand')
                ->waitForText('Loan Demand Collection Sheet')
                ->screenshot('06-accountant/83-accountant-loan-demand');

            // ── Logout ───────────────────────────────────────────
            $browser->visit('/accountant/dashboard')
                ->waitForText('Accountant Dashboard')
                ->click('a[data-toggle="dropdown"]')
                ->pause(500)
                ->press('Logout')
                ->waitForLocation('/login')
                ->screenshot('06-accountant/84-accountant-logged-out');
        });
    }
}
