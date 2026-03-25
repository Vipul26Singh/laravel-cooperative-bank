<?php

namespace Tests\Feature\RoleAccess;

use App\Models\{User, Role};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function makeUser(string $roleName): User
    {
        $role = Role::where('name', $roleName)->first();

        return User::create([
            'name'      => "$roleName User",
            'email'     => strtolower($roleName) . '@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $role->id,
            'is_active' => true,
        ]);
    }

    // ── SuperAdmin routes ──────────────────────────────────────────

    public function test_superadmin_can_access_superadmin_routes(): void
    {
        $user = $this->makeUser('SuperAdmin');

        $this->actingAs($user)->get('/superadmin/dashboard')->assertOk();
        $this->actingAs($user)->get('/superadmin/branches')->assertOk();
        $this->actingAs($user)->get('/superadmin/users')->assertOk();
        $this->actingAs($user)->get('/superadmin/loan-types')->assertOk();
        $this->actingAs($user)->get('/superadmin/fd-setups')->assertOk();
        $this->actingAs($user)->get('/superadmin/account-types')->assertOk();
        $this->actingAs($user)->get('/superadmin/company-setup')->assertOk();
    }

    public function test_manager_cannot_access_superadmin_routes(): void
    {
        $user = $this->makeUser('Manager');

        $this->actingAs($user)->get('/superadmin/dashboard')->assertForbidden();
        $this->actingAs($user)->get('/superadmin/branches')->assertForbidden();
        $this->actingAs($user)->get('/superadmin/users')->assertForbidden();
    }

    public function test_clerk_cannot_access_superadmin_routes(): void
    {
        $user = $this->makeUser('Clerk');

        $this->actingAs($user)->get('/superadmin/dashboard')->assertForbidden();
    }

    public function test_cashier_cannot_access_superadmin_routes(): void
    {
        $user = $this->makeUser('Cashier');

        $this->actingAs($user)->get('/superadmin/dashboard')->assertForbidden();
    }

    public function test_accountant_cannot_access_superadmin_routes(): void
    {
        $user = $this->makeUser('Accountant');

        $this->actingAs($user)->get('/superadmin/dashboard')->assertForbidden();
    }

    // ── Manager routes — SuperAdmin also has access ────────────────

    public function test_superadmin_can_access_manager_routes(): void
    {
        $user = $this->makeUser('SuperAdmin');

        $this->actingAs($user)->get('/manager/dashboard')->assertOk();
    }

    public function test_manager_can_access_manager_routes(): void
    {
        $user = $this->makeUser('Manager');

        $this->actingAs($user)->get('/manager/dashboard')->assertOk();
    }

    public function test_clerk_cannot_access_manager_routes(): void
    {
        $user = $this->makeUser('Clerk');

        $this->actingAs($user)->get('/manager/dashboard')->assertForbidden();
    }

    // ── Clerk routes — Manager + SuperAdmin also have access ───────

    public function test_clerk_can_access_clerk_routes(): void
    {
        $user = $this->makeUser('Clerk');

        $this->actingAs($user)->get('/clerk/dashboard')->assertOk();
        $this->actingAs($user)->get('/clerk/customers')->assertOk();
    }

    public function test_manager_can_access_clerk_routes(): void
    {
        $user = $this->makeUser('Manager');

        $this->actingAs($user)->get('/clerk/dashboard')->assertOk();
    }

    public function test_cashier_cannot_access_clerk_routes(): void
    {
        $user = $this->makeUser('Cashier');

        $this->actingAs($user)->get('/clerk/dashboard')->assertForbidden();
    }

    // ── Cashier routes ─────────────────────────────────────────────

    public function test_cashier_can_access_cashier_routes(): void
    {
        $user = $this->makeUser('Cashier');

        $this->actingAs($user)->get('/cashier/dashboard')->assertOk();
    }

    public function test_superadmin_can_access_cashier_routes(): void
    {
        $user = $this->makeUser('SuperAdmin');

        $this->actingAs($user)->get('/cashier/dashboard')->assertOk();
    }

    public function test_clerk_cannot_access_cashier_routes(): void
    {
        $user = $this->makeUser('Clerk');

        $this->actingAs($user)->get('/cashier/dashboard')->assertForbidden();
    }

    // ── Accountant routes ──────────────────────────────────────────

    public function test_accountant_can_access_accountant_routes(): void
    {
        $user = $this->makeUser('Accountant');

        $this->actingAs($user)->get('/accountant/dashboard')->assertOk();
    }

    public function test_superadmin_can_access_accountant_routes(): void
    {
        $user = $this->makeUser('SuperAdmin');

        $this->actingAs($user)->get('/accountant/dashboard')->assertOk();
    }

    public function test_clerk_cannot_access_accountant_routes(): void
    {
        $user = $this->makeUser('Clerk');

        $this->actingAs($user)->get('/accountant/dashboard')->assertForbidden();
    }

    // ── Unauthenticated access ─────────────────────────────────────

    public function test_guest_cannot_access_any_dashboard(): void
    {
        $this->get('/superadmin/dashboard')->assertRedirect('/login');
        $this->get('/manager/dashboard')->assertRedirect('/login');
        $this->get('/clerk/dashboard')->assertRedirect('/login');
        $this->get('/cashier/dashboard')->assertRedirect('/login');
        $this->get('/accountant/dashboard')->assertRedirect('/login');
    }
}
