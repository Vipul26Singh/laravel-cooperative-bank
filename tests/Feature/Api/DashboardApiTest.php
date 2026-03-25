<?php

namespace Tests\Feature\Api;

use App\Models\{User, Role, Branch, Customer, BankAccount, AccountType};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AdminUserSeeder::class);
        $this->admin = User::where('email', 'admin@coopbank.com')->first();
    }

    public function test_dashboard_stats_structure(): void
    {
        Sanctum::actingAs($this->admin);
        $this->getJson('/api/dashboard/stats')
            ->assertOk()
            ->assertJsonStructure([
                'customers'          => ['total', 'pending'],
                'loans'              => ['active', 'pending', 'total_outstanding'],
                'accounts'           => ['total', 'total_balance'],
                'fd_accounts'        => ['total', 'matured', 'total_amount'],
                'today_transactions' => ['count', 'total_deposit', 'total_withdraw'],
            ]);
    }

    public function test_dashboard_stats_counts(): void
    {
        $branch = Branch::create(['name' => 'B1', 'code' => 'BR1', 'address' => 'A', 'is_active' => true]);

        Customer::create([
            'customer_number' => 1001, 'full_name' => 'Approved',
            'gender' => 'Male', 'mobile' => '111', 'residential_address' => 'A',
            'branch_id' => $branch->id, 'approval_status' => 'approved',
            'is_member_active' => true, 'created_by' => $this->admin->id,
        ]);
        Customer::create([
            'customer_number' => 1002, 'full_name' => 'Pending',
            'gender' => 'Female', 'mobile' => '222', 'residential_address' => 'B',
            'branch_id' => $branch->id, 'approval_status' => 'pending',
            'is_member_active' => false, 'created_by' => $this->admin->id,
        ]);

        Sanctum::actingAs($this->admin);
        $response = $this->getJson('/api/dashboard/stats')->assertOk();

        $this->assertEquals(1, $response->json('customers.total'));
        $this->assertEquals(1, $response->json('customers.pending'));
    }

    public function test_dashboard_requires_auth(): void
    {
        $this->getJson('/api/dashboard/stats')->assertUnauthorized();
    }
}
