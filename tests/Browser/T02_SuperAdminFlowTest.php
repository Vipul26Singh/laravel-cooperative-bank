<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T02_SuperAdminFlowTest extends DuskTestCase
{
    public function test_full_superadmin_flow(): void
    {
        $this->browse(function (Browser $browser) {

            // ── Login ────────────────────────────────────────────
            $browser->visit('/login')
                ->type('email', 'admin@coopbank.com')
                ->type('password', 'Admin@123')
                ->press('Sign In')
                ->waitForLocation('/superadmin/dashboard');

            // ── Dashboard ────────────────────────────────────────
            $browser->assertSee('Total Customers')
                ->assertSee('Branches')
                ->assertSee('Employees')
                ->assertSee('Active Loans')
                ->assertSee('Quick Links')
                ->screenshot('02-superadmin/10-superadmin-dashboard');

            // ════════════════════════════════════════════════════
            // BRANCHES: index → create → index → edit → index
            // ════════════════════════════════════════════════════

            $browser->visit('/superadmin/branches')
                ->assertSee('Manage Branches')
                ->assertSee('New Branch')
                ->screenshot('02-superadmin/11-branches-index-empty');

            $browser->visit('/superadmin/branches/create')
                ->assertSee('New Branch')
                ->screenshot('02-superadmin/12-branch-create-form')
                ->type('name', 'Downtown Branch')
                ->type('code', 'BRDWN')
                ->type('address', '123 Main Street, Mumbai')
                ->screenshot('02-superadmin/13-branch-form-filled')
                ->press('Save Branch')
                ->waitForText('Branch created successfully')
                ->assertSee('Downtown Branch')
                ->assertSee('BRDWN')
                ->screenshot('02-superadmin/14-branch-created');

            // View detail page
            $browser->clickLink('View')
                ->waitForText('Branch Details')
                ->assertSee('Downtown Branch')
                ->assertSee('BRDWN')
                ->assertSee('Back to Branches')
                ->screenshot('02-superadmin/15-branch-show');

            $browser->clickLink('Edit')
                ->waitForText('Edit:')
                ->assertSee('Downtown Branch')
                ->screenshot('02-superadmin/16-branch-edit-form')
                ->clear('name')
                ->type('name', 'Downtown HQ')
                ->press('Update Branch')
                ->waitForText('Branch updated successfully')
                ->assertSee('Downtown HQ')
                ->screenshot('02-superadmin/16-branch-updated');

            // ════════════════════════════════════════════════════
            // USERS: index → create → index → edit
            // ════════════════════════════════════════════════════

            $browser->visit('/superadmin/users')
                ->assertSee('Manage Users')
                ->assertSee('Super Administrator')
                ->screenshot('02-superadmin/17-users-index');

            $browser->visit('/superadmin/users/create')
                ->assertSee('New User')
                ->assertSee('Full Name')
                ->assertSee('Role')
                ->assertSee('Branch')
                ->screenshot('02-superadmin/18-user-create-form')
                ->type('name', 'Test Clerk')
                ->type('email', 'duskclerk@coopbank.com')
                ->type('password', 'Clerk@123')
                ->type('password_confirmation', 'Clerk@123')
                ->select('role_id', '3')
                ->screenshot('02-superadmin/19-user-form-filled')
                ->press('Save User')
                ->waitForText('User created successfully')
                ->assertSee('duskclerk@coopbank.com')
                ->screenshot('02-superadmin/20-user-created');

            // Users index shows the new user — click View
            $browser->assertSee('Test Clerk')
                ->assertSee('Clerk')
                ->screenshot('02-superadmin/21-users-index-with-data');

            // View user detail page
            $browser->clickLink('View')
                ->waitForText('User Details')
                ->assertSee('duskclerk@coopbank.com')
                ->assertSee('Back to Users')
                ->screenshot('02-superadmin/22-user-show');

            // ════════════════════════════════════════════════════
            // LOAN TYPES: index → create → index → edit
            // ════════════════════════════════════════════════════

            $browser->visit('/superadmin/loan-types')
                ->assertSee('Manage Loan Types')
                ->screenshot('02-superadmin/23-loan-types-index-empty');

            $browser->visit('/superadmin/loan-types/create')
                ->assertSee('New Loan Type')
                ->assertSee('Interest Rate')
                ->assertSee('Duration')
                ->assertSee('Max Amount')
                ->screenshot('02-superadmin/24-loan-type-create-form')
                ->type('name', 'Home Loan')
                ->type('interest_rate', '8.5')
                ->type('duration_months', '240')
                ->type('max_amount', '5000000')
                ->type('num_installments', '240')
                ->press('Save Loan Type')
                ->waitForText('Loan type created successfully')
                ->assertSee('Home Loan')
                ->screenshot('02-superadmin/25-loan-type-created');

            $browser->clickLink('Edit')
                ->waitForText('Edit:')
                ->assertSee('Home Loan')
                ->screenshot('02-superadmin/26-loan-type-edit-form');

            $browser->visit('/superadmin/loan-types')
                ->assertSee('Home Loan')
                ->screenshot('02-superadmin/27-loan-types-index-with-data');

            // ════════════════════════════════════════════════════
            // FD SETUPS: index → create → index → edit
            // ════════════════════════════════════════════════════

            $browser->visit('/superadmin/fd-setups')
                ->assertSee('Manage FD Setups')
                ->screenshot('02-superadmin/28-fd-setups-index-empty');

            $browser->visit('/superadmin/fd-setups/create')
                ->assertSee('New FD Scheme')
                ->assertSee('Duration')
                ->assertSee('Interest Rate')
                ->screenshot('02-superadmin/29-fd-setup-create-form')
                ->type('duration_days', '365')
                ->type('interest_rate', '7.25')
                ->type('description', '1 Year Fixed Deposit @ 7.25%')
                ->press('Save FD Setup')
                ->waitForText('FD setup created successfully')
                ->screenshot('02-superadmin/30-fd-setup-created');

            $browser->clickLink('Edit')
                ->waitForText('Edit FD Scheme')
                ->screenshot('02-superadmin/31-fd-setup-edit-form');

            $browser->visit('/superadmin/fd-setups')
                ->assertSee('7.25')
                ->screenshot('02-superadmin/32-fd-setups-index-with-data');

            // ════════════════════════════════════════════════════
            // ACCOUNT TYPES: index → create → index → edit
            // ════════════════════════════════════════════════════

            $browser->visit('/superadmin/account-types')
                ->assertSee('Manage Account Types')
                ->screenshot('02-superadmin/33-account-types-index-empty');

            $browser->visit('/superadmin/account-types/create')
                ->assertSee('New Account Type')
                ->assertSee('Type')
                ->assertSee('Minimum Balance')
                ->screenshot('02-superadmin/34-account-type-create-form')
                ->type('name', 'Savings Regular')
                ->select('type', 'Savings')
                ->type('minimum_balance', '500')
                ->type('interest_rate', '3.5')
                ->press('Save Account Type')
                ->waitForText('Account type created successfully')
                ->assertSee('Savings Regular')
                ->screenshot('02-superadmin/35-account-type-created');

            $browser->clickLink('Edit')
                ->waitForText('Edit:')
                ->assertSee('Savings Regular')
                ->screenshot('02-superadmin/36-account-type-edit-form');

            $browser->visit('/superadmin/account-types')
                ->assertSee('Savings Regular')
                ->assertSee('Savings')
                ->screenshot('02-superadmin/37-account-types-index-with-data');

            // ════════════════════════════════════════════════════
            // COMPANY SETUP
            // ════════════════════════════════════════════════════

            $browser->visit('/superadmin/company-setup')
                ->assertSee('Company Configuration')
                ->assertSee('Company Name')
                ->assertSee('GST No')
                ->assertSee('PAN No')
                ->screenshot('02-superadmin/38-company-setup-form')
                ->type('name', 'National Cooperative Bank Ltd')
                ->type('address', '100 Bank Street')
                ->type('phone', '022-12345678')
                ->type('email', 'info@natcoopbank.com')
                ->type('gst_no', '27AABCN1234F1Z5')
                ->type('pan_no', 'AABCN1234F')
                ->press('Save Settings')
                ->waitForText('Company settings saved successfully')
                ->screenshot('02-superadmin/39-company-setup-saved');

            // ════════════════════════════════════════════════════
            // TASK SCHEDULER
            // ════════════════════════════════════════════════════

            // Seed scheduled tasks
            (new \App\Services\TaskSchedulerService())->seedDefaults();

            $browser->visit('/superadmin/task-scheduler')
                ->waitForText('Scheduled Tasks')
                ->assertSee('Process FD Maturity')
                ->assertSee('Process Loan OD Interest')
                ->assertSee('Drain Job Queue')
                ->assertSee('Send Overdue EMI Reminders')
                ->assertSee('Daily Transaction Report')
                ->screenshot('02-superadmin/42-task-scheduler-index');

            // Edit first task's schedule
            $browser->click('a[title="Edit Schedule"]')
                ->waitForText('Edit Task Schedule')
                ->assertSee('Frequency')
                ->assertSee('Run Time')
                ->screenshot('02-superadmin/43-task-scheduler-edit');

            // View logs for first task
            $browser->visit('/superadmin/task-scheduler')
                ->waitForText('Scheduled Tasks')
                ->click('a[title="View Logs"]')
                ->waitForText('Task Run History')
                ->screenshot('02-superadmin/44-task-scheduler-logs');

            // ════════════════════════════════════════════════════
            // QUEUE MONITOR
            // ════════════════════════════════════════════════════

            // Insert fake pending jobs directly into the jobs table
            $now = now()->timestamp;
            foreach ([
                ['queue' => 'default', 'name' => 'App\\Jobs\\SendEmailJob'],
                ['queue' => 'default', 'name' => 'App\\Jobs\\SendEmailJob'],
                ['queue' => 'notifications', 'name' => 'App\\Jobs\\SendSmsJob'],
            ] as $job) {
                \Illuminate\Support\Facades\DB::table('jobs')->insert([
                    'queue'        => $job['queue'],
                    'payload'      => json_encode(['displayName' => $job['name'], 'job' => 'Illuminate\\Queue\\CallQueuedHandler@call']),
                    'attempts'     => 0,
                    'reserved_at'  => null,
                    'available_at' => $now,
                    'created_at'   => $now,
                ]);
            }

            // Insert a fake failed job
            \Illuminate\Support\Facades\DB::table('failed_jobs')->insert([
                'uuid'       => \Illuminate\Support\Str::uuid(),
                'connection' => 'database',
                'queue'      => 'notifications',
                'payload'    => json_encode(['displayName' => 'App\\Jobs\\SendEmailJob', 'job' => 'test']),
                'exception'  => 'RuntimeException: SMTP connection refused at MailService.php:42',
                'failed_at'  => now(),
            ]);

            $browser->visit('/superadmin/queue-monitor')
                ->waitForText('Queue Monitor')
                ->assertSee('Pending Jobs')
                ->assertSee('Failed Jobs')
                // Verify pending jobs show up
                ->assertSee('SendEmailJob')
                ->assertSee('SendSmsJob')
                ->assertSee('notifications')
                ->screenshot('02-superadmin/45-queue-monitor-pending');

            // Scroll to failed jobs section
            $browser->script('window.scrollTo(0, document.body.scrollHeight)');
            $browser->pause(500)
                ->assertSee('SMTP connection refused')
                ->screenshot('02-superadmin/46-queue-monitor-failed');

            // ════════════════════════════════════════════════════
            // SIDEBAR FULL NAVIGATION
            // ════════════════════════════════════════════════════

            $browser->clickLink('Dashboard')
                ->waitForText('SuperAdmin Dashboard')
                ->screenshot('02-superadmin/40a-nav-dashboard')
                ->clickLink('Branches')
                ->waitForText('Manage Branches')
                ->screenshot('02-superadmin/40b-nav-branches')
                ->clickLink('Users')
                ->waitForText('Manage Users')
                ->screenshot('02-superadmin/40c-nav-users')
                ->clickLink('Loan Types')
                ->waitForText('Manage Loan Types')
                ->screenshot('02-superadmin/40d-nav-loan-types')
                ->clickLink('FD Setups')
                ->waitForText('Manage FD Setups')
                ->screenshot('02-superadmin/40e-nav-fd-setups')
                ->clickLink('Account Types')
                ->waitForText('Manage Account Types')
                ->screenshot('02-superadmin/40f-nav-account-types')
                ->clickLink('Company Setup')
                ->waitForText('Company Configuration')
                ->screenshot('02-superadmin/40g-nav-company-setup')
                ->clickLink('Task Scheduler')
                ->waitForText('Scheduled Tasks')
                ->screenshot('02-superadmin/40h-nav-task-scheduler')
                ->clickLink('Queue Monitor')
                ->waitForText('Queue Monitor')
                ->screenshot('02-superadmin/40i-nav-queue-monitor');

            // ── Logout ───────────────────────────────────────────
            $browser->click('a[data-toggle="dropdown"]')
                ->pause(500)
                ->press('Logout')
                ->waitForLocation('/login')
                ->screenshot('02-superadmin/41-superadmin-logged-out');
        });
    }
}
