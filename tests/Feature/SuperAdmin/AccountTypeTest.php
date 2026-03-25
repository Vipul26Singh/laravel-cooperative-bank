<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\{User, Role, AccountType};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTypeTest extends TestCase
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

    public function test_account_types_index_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/account-types')
            ->assertOk()
            ->assertSee('Manage Account Types');
    }

    public function test_can_create_account_type(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/account-types', [
                'name'            => 'Savings Account',
                'type'            => 'Savings',
                'minimum_balance' => 500,
                'interest_rate'   => 3.5,
                'is_active'       => 1,
            ])
            ->assertRedirect(route('superadmin.account-types.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('account_types', ['name' => 'Savings Account', 'type' => 'Savings']);
    }

    public function test_create_account_type_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/account-types', [])
            ->assertSessionHasErrors(['name', 'type']);
    }

    public function test_can_update_account_type(): void
    {
        $at = AccountType::create([
            'name'      => 'Old Type',
            'type'      => 'Savings',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->put("/superadmin/account-types/{$at->id}", [
                'name'      => 'Updated Type',
                'type'      => 'Current',
                'is_active' => 1,
            ])
            ->assertRedirect(route('superadmin.account-types.index'));

        $this->assertDatabaseHas('account_types', ['id' => $at->id, 'name' => 'Updated Type', 'type' => 'Current']);
    }

    public function test_destroy_deactivates_account_type(): void
    {
        $at = AccountType::create([
            'name'      => 'Test',
            'type'      => 'Savings',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->delete("/superadmin/account-types/{$at->id}")
            ->assertRedirect(route('superadmin.account-types.index'));

        $this->assertDatabaseHas('account_types', ['id' => $at->id, 'is_active' => false]);
    }
}
