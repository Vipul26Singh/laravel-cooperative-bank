<?php

namespace Tests\Feature\Auth;

use App\Models\{User, Role};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function createUser(string $roleName = 'SuperAdmin', bool $active = true): User
    {
        $role = Role::where('name', $roleName)->first();

        return User::create([
            'name'      => 'Test User',
            'email'     => 'test@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $role->id,
            'is_active' => $active,
        ]);
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200)->assertSee('Sign in');
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get('/superadmin/dashboard')->assertRedirect('/login');
    }

    public function test_home_shows_landing_page_for_guest(): void
    {
        $this->get('/')->assertOk()->assertSee('CoopBank ERP');
    }

    public function test_successful_login_redirects_to_dashboard(): void
    {
        $this->createUser('SuperAdmin');

        $this->post('/login', [
            'email'    => 'test@coopbank.com',
            'password' => 'Password@123',
        ])->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->createUser('SuperAdmin');

        $this->post('/login', [
            'email'    => 'test@coopbank.com',
            'password' => 'WrongPassword',
        ])->assertSessionHasErrors('email');
    }

    public function test_login_fails_for_inactive_user(): void
    {
        $this->createUser('SuperAdmin', active: false);

        $this->post('/login', [
            'email'    => 'test@coopbank.com',
            'password' => 'Password@123',
        ])->assertSessionHasErrors('email');
    }

    public function test_login_validates_required_fields(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_validates_email_format(): void
    {
        $this->post('/login', [
            'email'    => 'not-an-email',
            'password' => 'Password@123',
        ])->assertSessionHasErrors('email');
    }

    public function test_logout_works(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_home_redirects_authenticated_superadmin_to_dashboard(): void
    {
        $user = $this->createUser('SuperAdmin');
        $this->actingAs($user);

        $this->get('/')->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_home_redirects_each_role_to_correct_dashboard(): void
    {
        $roles = [
            'SuperAdmin' => 'superadmin.dashboard',
            'Manager'    => 'manager.dashboard',
            'Clerk'      => 'clerk.dashboard',
            'Cashier'    => 'cashier.dashboard',
            'Accountant' => 'accountant.dashboard',
        ];

        foreach ($roles as $roleName => $expectedRoute) {
            $role = Role::where('name', $roleName)->first();
            $user = User::create([
                'name'      => "$roleName User",
                'email'     => strtolower($roleName) . '@coopbank.com',
                'password'  => bcrypt('Password@123'),
                'role_id'   => $role->id,
                'is_active' => true,
            ]);

            $this->actingAs($user)
                ->get('/')
                ->assertRedirect(route($expectedRoute));
        }
    }

    public function test_login_redirects_each_role_to_correct_dashboard(): void
    {
        $roles = [
            'Manager'    => 'manager.dashboard',
            'Clerk'      => 'clerk.dashboard',
            'Cashier'    => 'cashier.dashboard',
            'Accountant' => 'accountant.dashboard',
        ];

        foreach ($roles as $roleName => $expectedRoute) {
            $role = Role::where('name', $roleName)->first();
            User::create([
                'name'      => "$roleName User",
                'email'     => strtolower($roleName) . '@test.com',
                'password'  => bcrypt('Password@123'),
                'role_id'   => $role->id,
                'is_active' => true,
            ]);

            $this->post('/login', [
                'email'    => strtolower($roleName) . '@test.com',
                'password' => 'Password@123',
            ])->assertRedirect(route($expectedRoute));

            // Logout for next iteration
            $this->post('/logout');
        }
    }
}
