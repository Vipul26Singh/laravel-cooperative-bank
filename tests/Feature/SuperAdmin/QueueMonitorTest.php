<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\{User, Role};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueueMonitorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $role = Role::where('name', 'SuperAdmin')->first();
        $this->admin = User::create([
            'name' => 'Admin', 'email' => 'admin@coopbank.com',
            'password' => bcrypt('Admin@123'), 'role_id' => $role->id, 'is_active' => true,
        ]);
    }

    public function test_queue_monitor_index_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/queue-monitor')
            ->assertOk()
            ->assertSee('Queue Monitor')
            ->assertSee('Pending Jobs')
            ->assertSee('Failed Jobs');
    }

    public function test_queue_monitor_shows_empty_state(): void
    {
        $this->actingAs($this->admin)
            ->get('/superadmin/queue-monitor')
            ->assertSee('Queue is empty')
            ->assertSee('No failed jobs');
    }

    public function test_process_now_works(): void
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/queue-monitor/process')
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_non_superadmin_cannot_access(): void
    {
        $clerkRole = Role::where('name', 'Clerk')->first();
        $clerk = User::create([
            'name' => 'Clerk', 'email' => 'clerk@test.com',
            'password' => bcrypt('Pass@123'), 'role_id' => $clerkRole->id, 'is_active' => true,
        ]);

        $this->actingAs($clerk)
            ->get('/superadmin/queue-monitor')
            ->assertForbidden();
    }

    public function test_flush_clears_failed_jobs(): void
    {
        $this->actingAs($this->admin)
            ->delete('/superadmin/queue-monitor/flush')
            ->assertRedirect()
            ->assertSessionHas('success');
    }
}
