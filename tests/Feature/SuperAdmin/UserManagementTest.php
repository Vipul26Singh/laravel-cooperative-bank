<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\{User, Role, Branch};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $role = Role::where('name', 'SuperAdmin')->first();
        $this->admin = User::create([
            'name'      => 'Admin',
            'email'     => 'admin@coopbank.com',
            'password'  => bcrypt('Admin@123'),
            'role_id'   => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_users_index_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/users')
            ->assertOk()
            ->assertSee('Manage Users');
    }

    public function test_users_index_shows_existing_users(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/users')
            ->assertSee('Admin')
            ->assertSee('admin@coopbank.com');
    }

    public function test_user_create_form_loads_with_roles_and_branches(): void
    {
        Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->get('/superadmin/users/create')
            ->assertOk()
            ->assertSee('New User')
            ->assertSee('SuperAdmin')
            ->assertSee('Manager')
            ->assertSee('Main');
    }

    public function test_can_create_user(): void
    {
        $clerkRole = Role::where('name', 'Clerk')->first();

        $this->actingAs($this->admin)
            ->post('/superadmin/users', [
                'name'                  => 'Jane Doe',
                'email'                 => 'jane@coopbank.com',
                'password'              => 'Password@123',
                'password_confirmation' => 'Password@123',
                'role_id'               => $clerkRole->id,
                'is_active'             => 1,
            ])
            ->assertRedirect(route('superadmin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['email' => 'jane@coopbank.com', 'role_id' => $clerkRole->id]);
    }

    public function test_create_user_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/users', [])
            ->assertSessionHasErrors(['name', 'email', 'password', 'role_id']);
    }

    public function test_create_user_validates_unique_email(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/users', [
                'name'                  => 'Duplicate',
                'email'                 => 'admin@coopbank.com', // already exists
                'password'              => 'Password@123',
                'password_confirmation' => 'Password@123',
                'role_id'               => $this->admin->role_id,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_create_user_validates_password_confirmation(): void
    {
        $clerkRole = Role::where('name', 'Clerk')->first();

        $this->actingAs($this->admin)
            ->post('/superadmin/users', [
                'name'                  => 'Test',
                'email'                 => 'test@coopbank.com',
                'password'              => 'Password@123',
                'password_confirmation' => 'DifferentPassword',
                'role_id'               => $clerkRole->id,
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_can_update_user(): void
    {
        $clerkRole = Role::where('name', 'Clerk')->first();
        $user = User::create([
            'name'      => 'Old Name',
            'email'     => 'old@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $clerkRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->put("/superadmin/users/{$user->id}", [
                'name'      => 'New Name',
                'email'     => 'old@coopbank.com',
                'role_id'   => $clerkRole->id,
                'is_active' => 1,
            ])
            ->assertRedirect(route('superadmin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    public function test_update_user_password_is_optional(): void
    {
        $clerkRole = Role::where('name', 'Clerk')->first();
        $user = User::create([
            'name'      => 'Test',
            'email'     => 'clerk@coopbank.com',
            'password'  => bcrypt('OldPassword@123'),
            'role_id'   => $clerkRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->put("/superadmin/users/{$user->id}", [
                'name'      => 'Test Updated',
                'email'     => 'clerk@coopbank.com',
                'role_id'   => $clerkRole->id,
                'is_active' => 1,
                // no password field — should not change password
            ])
            ->assertRedirect(route('superadmin.users.index'));

        // Logout admin, then verify clerk can login with old password
        auth()->logout();
        $this->post('/login', [
            'email'    => 'clerk@coopbank.com',
            'password' => 'OldPassword@123',
        ])->assertRedirect(route('clerk.dashboard'));
    }

    public function test_destroy_deactivates_user(): void
    {
        $clerkRole = Role::where('name', 'Clerk')->first();
        $user = User::create([
            'name'      => 'Test',
            'email'     => 'deactivate@coopbank.com',
            'password'  => bcrypt('Password@123'),
            'role_id'   => $clerkRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->delete("/superadmin/users/{$user->id}")
            ->assertRedirect(route('superadmin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);
    }

    public function test_can_assign_user_to_branch(): void
    {
        $branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $clerkRole = Role::where('name', 'Clerk')->first();

        $this->actingAs($this->admin)
            ->post('/superadmin/users', [
                'name'                  => 'Branch Clerk',
                'email'                 => 'branch.clerk@coopbank.com',
                'password'              => 'Password@123',
                'password_confirmation' => 'Password@123',
                'role_id'               => $clerkRole->id,
                'branch_id'             => $branch->id,
                'is_active'             => 1,
            ])
            ->assertRedirect(route('superadmin.users.index'));

        $this->assertDatabaseHas('users', [
            'email'     => 'branch.clerk@coopbank.com',
            'branch_id' => $branch->id,
        ]);
    }
}
