<?php

namespace Tests\Browser;

use App\Models\{User, Role, Branch};
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T04_ClerkFlowTest extends DuskTestCase
{
    public function test_full_clerk_flow(): void
    {
        $branch = Branch::firstOrCreate(['code' => 'BRCLK'], ['name' => 'Clerk Branch', 'address' => 'Clerk St', 'is_active' => true]);
        $role = Role::where('name', 'Clerk')->first();
        User::firstOrCreate(['email' => 'clk@coopbank.com'], [
            'name' => 'Dusk Clerk', 'password' => bcrypt('Clerk@123'),
            'role_id' => $role->id, 'branch_id' => $branch->id, 'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {

            // ── Login ────────────────────────────────────────────
            $browser->visit('/login')
                ->type('email', 'clk@coopbank.com')
                ->type('password', 'Clerk@123')
                ->press('Sign In')
                ->waitForText('Clerk Dashboard', 15);

            // ── Dashboard ────────────────────────────────────────
            $browser->assertSee('Clerk Dashboard')
                ->screenshot('04-clerk/60-clerk-dashboard');

            // ── Customers Index ──────────────────────────────────
            $browser->visit('/clerk/customers')
                ->waitForText('Customers')
                ->screenshot('04-clerk/61-clerk-customer-index');

            // ── Customer Registration Form ───────────────────────
            $browser->visit('/clerk/customers/create')
                ->waitForText('Register New Customer')
                ->assertSee('Personal Information')
                ->assertSee('Contact Information')
                ->screenshot('04-clerk/62-customer-create-form');

            // ── Loan Applications Index ──────────────────────────
            $browser->visit('/clerk/loan-applications')
                ->waitForText('My Loan Applications')
                ->screenshot('04-clerk/63-clerk-loan-applications');

            // ── Loan Application Create Form ─────────────────────
            $browser->visit('/clerk/loan-applications/create')
                ->waitForText('Submit Loan Application')
                ->assertSee('Customer')
                ->assertSee('Loan Type')
                ->assertSee('Purpose')
                ->screenshot('04-clerk/64-clerk-loan-application-create');

            // ── Logout ───────────────────────────────────────────
            $browser->visit('/clerk/dashboard')
                ->waitForText('Clerk Dashboard')
                ->click('a[data-toggle="dropdown"]')
                ->pause(500)
                ->press('Logout')
                ->waitForLocation('/login')
                ->screenshot('04-clerk/65-clerk-logged-out');
        });
    }
}
