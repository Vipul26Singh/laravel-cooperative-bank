<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\{User, Role, LoanType};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTypeTest extends TestCase
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

    public function test_loan_types_index_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/loan-types')
            ->assertOk()
            ->assertSee('Manage Loan Types');
    }

    public function test_loan_type_create_form_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/loan-types/create')
            ->assertOk()
            ->assertSee('New Loan Type');
    }

    public function test_can_create_loan_type(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/loan-types', [
                'name'             => 'Personal Loan',
                'interest_rate'    => 12.5,
                'duration_months'  => 60,
                'max_amount'       => 500000,
                'num_installments' => 60,
                'frequency'        => 'MONTHLY',
                'description'      => 'General purpose personal loan',
                'is_active'        => 1,
            ])
            ->assertRedirect(route('superadmin.loan-types.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('loan_types', ['name' => 'Personal Loan']);
    }

    public function test_create_loan_type_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/loan-types', [])
            ->assertSessionHasErrors(['name', 'interest_rate', 'duration_months', 'max_amount', 'num_installments']);
    }

    public function test_can_update_loan_type(): void
    {
        $lt = LoanType::create([
            'name'             => 'Old Loan',
            'interest_rate'    => 10,
            'duration_months'  => 12,
            'max_amount'       => 100000,
            'num_installments' => 12,
            'is_active'        => true,
        ]);

        $this->actingAs($this->admin)
            ->put("/superadmin/loan-types/{$lt->id}", [
                'name'             => 'Updated Loan',
                'interest_rate'    => 15,
                'duration_months'  => 24,
                'max_amount'       => 200000,
                'num_installments' => 24,
                'is_active'        => 1,
            ])
            ->assertRedirect(route('superadmin.loan-types.index'));

        $this->assertDatabaseHas('loan_types', ['id' => $lt->id, 'name' => 'Updated Loan']);
    }

    public function test_destroy_deactivates_loan_type(): void
    {
        $lt = LoanType::create([
            'name'             => 'Test Loan',
            'interest_rate'    => 10,
            'duration_months'  => 12,
            'max_amount'       => 100000,
            'num_installments' => 12,
            'is_active'        => true,
        ]);

        $this->actingAs($this->admin)
            ->delete("/superadmin/loan-types/{$lt->id}")
            ->assertRedirect(route('superadmin.loan-types.index'));

        $this->assertDatabaseHas('loan_types', ['id' => $lt->id, 'is_active' => false]);
    }
}
