<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\{User, Role, CompanySetup};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySetupTest extends TestCase
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

    public function test_company_setup_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/company-setup')
            ->assertOk()
            ->assertSee('Company Configuration');
    }

    public function test_can_save_company_setup(): void
    {
        $this->actingAs($this->admin)
            ->put('/superadmin/company-setup', [
                'name'    => 'Test Cooperative Bank',
                'address' => '456 Bank Street',
                'phone'   => '011-12345678',
                'email'   => 'info@coopbank.com',
            ])
            ->assertRedirect(route('superadmin.company-setup.show'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('company_setup', ['name' => 'Test Cooperative Bank']);
    }

    public function test_company_setup_validates_name(): void
    {
        $this->actingAs($this->admin)
            ->put('/superadmin/company-setup', [])
            ->assertSessionHasErrors('name');
    }

    public function test_company_setup_updates_existing_record(): void
    {
        $this->actingAs($this->admin)
            ->put('/superadmin/company-setup', ['name' => 'First Name']);

        $this->actingAs($this->admin)
            ->put('/superadmin/company-setup', ['name' => 'Second Name'])
            ->assertRedirect(route('superadmin.company-setup.show'));

        $this->assertEquals(1, CompanySetup::count());
        $this->assertDatabaseHas('company_setup', ['name' => 'Second Name']);
    }
}
