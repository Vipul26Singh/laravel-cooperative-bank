<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T01_LoginFlowTest extends DuskTestCase
{
    public function test_full_login_flow(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Login page renders
            $browser->visit('/login')
                ->assertSee('Sign in to start your session')
                ->assertVisible('input[name="email"]')
                ->assertVisible('input[name="password"]')
                ->screenshot('01-auth/01-login-page');

            // 2. Invalid credentials show error
            $browser->type('email', 'admin@coopbank.com')
                ->type('password', 'WrongPassword')
                ->press('Sign In')
                ->waitForText('Invalid credentials')
                ->assertSee('Invalid credentials')
                ->screenshot('01-auth/02-login-error');

            // 3. Valid credentials redirect to dashboard
            $browser->visit('/login')
                ->type('email', 'admin@coopbank.com')
                ->type('password', 'Admin@123')
                ->screenshot('01-auth/03-login-filled')
                ->press('Sign In')
                ->waitForLocation('/superadmin/dashboard')
                ->assertSee('SuperAdmin Dashboard')
                ->screenshot('01-auth/04-superadmin-dashboard');

            // 4. Logout works
            $browser->click('a[data-toggle="dropdown"]')
                ->pause(500)
                ->screenshot('01-auth/05-logout-dropdown')
                ->press('Logout')
                ->waitForLocation('/login')
                ->assertPathIs('/login')
                ->screenshot('01-auth/06-logged-out');
        });
    }
}
