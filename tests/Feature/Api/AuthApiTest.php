<?php

namespace Tests\Feature\Api;

use App\Models\{User, Role};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AdminUserSeeder::class);
    }

    public function test_login_returns_token(): void
    {
        $this->postJson('/api/login', [
            'email'    => 'admin@coopbank.com',
            'password' => 'Admin@123',
        ])->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->postJson('/api/login', [
            'email'    => 'admin@coopbank.com',
            'password' => 'WrongPassword',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_login_fails_for_inactive_user(): void
    {
        $role = Role::where('name', 'Clerk')->first();
        User::create([
            'name' => 'Inactive', 'email' => 'inactive@test.com',
            'password' => bcrypt('Pass@123'), 'role_id' => $role->id,
            'is_active' => false,
        ]);

        $this->postJson('/api/login', [
            'email' => 'inactive@test.com', 'password' => 'Pass@123',
        ])->assertUnprocessable();
    }

    public function test_login_validates_required_fields(): void
    {
        $this->postJson('/api/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_me_returns_user_info(): void
    {
        $token = $this->postJson('/api/login', [
            'email' => 'admin@coopbank.com', 'password' => 'Admin@123',
        ])->json('token');

        $this->getJson('/api/me', ['Authorization' => "Bearer $token"])
            ->assertOk()
            ->assertJsonFragment(['email' => 'admin@coopbank.com']);
    }

    public function test_me_fails_without_token(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_logout_returns_success(): void
    {
        $user = User::where('email', 'admin@coopbank.com')->first();
        $token = $user->createToken('test')->plainTextToken;

        $this->postJson('/api/logout', [], ['Authorization' => "Bearer $token"])
            ->assertOk()
            ->assertJsonFragment(['message' => 'Logged out successfully.']);

        // Token should be deleted from DB
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_all_api_routes_require_auth(): void
    {
        $this->getJson('/api/customers')->assertUnauthorized();
        $this->getJson('/api/bank-accounts')->assertUnauthorized();
        $this->getJson('/api/transactions')->assertUnauthorized();
        $this->getJson('/api/loans')->assertUnauthorized();
        $this->getJson('/api/fd-accounts')->assertUnauthorized();
        $this->getJson('/api/dashboard/stats')->assertUnauthorized();
    }
}
