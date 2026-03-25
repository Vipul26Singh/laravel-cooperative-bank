<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\{User, Role, Branch};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchTest extends TestCase
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

    public function test_branches_index_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/branches')
            ->assertOk()
            ->assertSee('Manage Branches');
    }

    public function test_branches_index_shows_existing_branches(): void
    {
        Branch::create(['name' => 'Main Branch', 'code' => 'BR001', 'address' => 'Test Address', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->get('/superadmin/branches')
            ->assertSee('Main Branch')
            ->assertSee('BR001');
    }

    public function test_branch_create_form_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/branches/create')
            ->assertOk()
            ->assertSee('New Branch');
    }

    public function test_can_create_branch(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/branches', [
                'name'      => 'Downtown Branch',
                'code'      => 'BR002',
                'address'   => '123 Main St',
                'is_active' => 1,
            ])
            ->assertRedirect(route('superadmin.branches.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('branches', ['code' => 'BR002', 'name' => 'Downtown Branch']);
    }

    public function test_create_branch_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/branches', [])
            ->assertSessionHasErrors(['name', 'code']);
    }

    public function test_create_branch_validates_unique_code(): void
    {
        Branch::create(['name' => 'First', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->post('/superadmin/branches', [
                'name' => 'Second',
                'code' => 'BR001',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_branch_edit_form_loads(): void
    {
        $branch = Branch::create(['name' => 'Test', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->get("/superadmin/branches/{$branch->id}/edit")
            ->assertOk()
            ->assertSee('Edit:');
    }

    public function test_can_update_branch(): void
    {
        $branch = Branch::create(['name' => 'Old Name', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->put("/superadmin/branches/{$branch->id}", [
                'name'      => 'New Name',
                'code'      => 'BR001',
                'is_active' => 1,
            ])
            ->assertRedirect(route('superadmin.branches.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'name' => 'New Name']);
    }

    public function test_destroy_deactivates_branch(): void
    {
        $branch = Branch::create(['name' => 'Test', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->delete("/superadmin/branches/{$branch->id}")
            ->assertRedirect(route('superadmin.branches.index'));

        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'is_active' => false]);
    }
}
