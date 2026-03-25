<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\{User, Role, FdSetup};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FdSetupTest extends TestCase
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

    public function test_fd_setups_index_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/fd-setups')
            ->assertOk()
            ->assertSee('Manage FD Setups');
    }

    public function test_fd_setup_create_form_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/fd-setups/create')
            ->assertOk()
            ->assertSee('New FD Scheme');
    }

    public function test_can_create_fd_setup(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/fd-setups', [
                'description'    => '12 Month Fixed Deposit',
                'duration_days'  => 365,
                'interest_rate'  => 7.5,
                'is_active'      => 1,
            ])
            ->assertRedirect(route('superadmin.fd-setups.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('fd_setups', ['description' => '12 Month Fixed Deposit']);
    }

    public function test_create_fd_setup_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/fd-setups', [])
            ->assertSessionHasErrors(['description', 'duration_days', 'interest_rate']);
    }

    public function test_destroy_deactivates_fd_setup(): void
    {
        $fd = FdSetup::create([
            'description'   => 'Test FD',
            'duration_days' => 180,
            'interest_rate' => 6.5,
            'is_active'     => true,
        ]);

        $this->actingAs($this->admin)
            ->delete("/superadmin/fd-setups/{$fd->id}")
            ->assertRedirect(route('superadmin.fd-setups.index'));

        $this->assertDatabaseHas('fd_setups', ['id' => $fd->id, 'is_active' => false]);
    }
}
