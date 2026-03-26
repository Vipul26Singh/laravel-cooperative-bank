<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\{User, Role, ScheduledTask, TaskRunLog};
use App\Services\TaskSchedulerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskSchedulerTest extends TestCase
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

    private function createTask(array $overrides = []): ScheduledTask
    {
        return ScheduledTask::create(array_merge([
            'name'      => 'Test Task',
            'command'   => 'config:clear',
            'frequency' => 'daily',
            'is_active' => true,
        ], $overrides));
    }

    public function test_scheduler_index_loads(): void
    {
        $this->createTask();

        $this->actingAs($this->admin)
            ->get('/superadmin/task-scheduler')
            ->assertOk()
            ->assertSee('Task Scheduler')
            ->assertSee('Test Task');
    }

    public function test_toggle_disables_task(): void
    {
        $task = $this->createTask(['is_active' => true]);

        $this->actingAs($this->admin)
            ->patch("/superadmin/task-scheduler/{$task->id}/toggle")
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('scheduled_tasks', ['id' => $task->id, 'is_active' => false]);
    }

    public function test_toggle_enables_task(): void
    {
        $task = $this->createTask(['is_active' => false]);

        $this->actingAs($this->admin)
            ->patch("/superadmin/task-scheduler/{$task->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseHas('scheduled_tasks', ['id' => $task->id, 'is_active' => true]);
    }

    public function test_run_now_executes_and_logs(): void
    {
        $task = $this->createTask();

        $this->actingAs($this->admin)
            ->post("/superadmin/task-scheduler/{$task->id}/run")
            ->assertRedirect();

        $task->refresh();
        $this->assertNotNull($task->last_run_at);
        $this->assertNotNull($task->last_status);
        $this->assertContains($task->last_status, ['success', 'failed']);
        $this->assertEquals(1, $task->run_count);
        $this->assertDatabaseCount('task_run_logs', 1);
    }

    public function test_run_now_logs_failure(): void
    {
        $task = $this->createTask(['command' => 'nonexistent:command']);

        $this->actingAs($this->admin)
            ->post("/superadmin/task-scheduler/{$task->id}/run")
            ->assertRedirect();

        $task->refresh();
        $this->assertEquals('failed', $task->last_status);
        $this->assertEquals(1, $task->fail_count);
    }

    public function test_edit_schedule_page_loads(): void
    {
        $task = $this->createTask();

        $this->actingAs($this->admin)
            ->get("/superadmin/task-scheduler/{$task->id}/edit")
            ->assertOk()
            ->assertSee('Edit Task Schedule')
            ->assertSee($task->name);
    }

    public function test_update_schedule_to_daily_at_time(): void
    {
        $task = $this->createTask(['frequency' => 'hourly']);

        $this->actingAs($this->admin)
            ->put("/superadmin/task-scheduler/{$task->id}", [
                'frequency_type' => 'dailyAt',
                'run_time'       => '14:30',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('scheduled_tasks', [
            'id' => $task->id, 'frequency' => 'dailyAt:14:30',
        ]);
    }

    public function test_update_schedule_to_simple_frequency(): void
    {
        $task = $this->createTask(['frequency' => 'dailyAt:09:00']);

        $this->actingAs($this->admin)
            ->put("/superadmin/task-scheduler/{$task->id}", [
                'frequency_type' => 'everyThirtyMinutes',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('scheduled_tasks', [
            'id' => $task->id, 'frequency' => 'everyThirtyMinutes',
        ]);
    }

    public function test_logs_page_loads(): void
    {
        $task = $this->createTask();
        $task->logs()->create([
            'status' => 'success', 'duration_ms' => 150,
            'started_at' => now(), 'finished_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get("/superadmin/task-scheduler/{$task->id}/logs")
            ->assertOk()
            ->assertSee('Test Task')
            ->assertSee('150');
    }

    public function test_seeder_creates_default_tasks(): void
    {
        (new TaskSchedulerService())->seedDefaults();

        $this->assertDatabaseHas('scheduled_tasks', ['command' => 'bank:process-fd-maturity']);
        $this->assertDatabaseHas('scheduled_tasks', ['command' => 'bank:process-loan-od-interest']);
        $this->assertDatabaseHas('scheduled_tasks', ['command' => 'queue:work --stop-when-empty']);
    }

    public function test_seeder_is_idempotent(): void
    {
        $service = new TaskSchedulerService();
        $service->seedDefaults();
        $service->seedDefaults();

        $this->assertEquals(7, ScheduledTask::count());
    }

    public function test_non_superadmin_cannot_access(): void
    {
        $clerkRole = Role::where('name', 'Clerk')->first();
        $clerk = User::create([
            'name' => 'Clerk', 'email' => 'clerk@coopbank.com',
            'password' => bcrypt('Pass@123'), 'role_id' => $clerkRole->id, 'is_active' => true,
        ]);

        $this->actingAs($clerk)
            ->get('/superadmin/task-scheduler')
            ->assertForbidden();
    }
}
