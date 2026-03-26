<?php

use App\Models\ScheduledTask;
use App\Services\TaskSchedulerService;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled Tasks — DB-backed scheduler
|--------------------------------------------------------------------------
| Tasks are managed via SuperAdmin > Task Scheduler UI.
| Reads active tasks from the scheduled_tasks table.
| Fallback: if the table doesn't exist yet (pre-migration), use defaults.
*/

try {
    $tasks = ScheduledTask::where('is_active', true)->get();

    foreach ($tasks as $task) {
        $scheduled = Schedule::command($task->command);

        match (true) {
            str_starts_with($task->frequency, 'dailyAt:')
                => $scheduled->dailyAt(str_replace('dailyAt:', '', $task->frequency)),
            $task->frequency === 'daily'               => $scheduled->daily(),
            $task->frequency === 'hourly'              => $scheduled->hourly(),
            $task->frequency === 'everyFiveMinutes'    => $scheduled->everyFiveMinutes(),
            $task->frequency === 'everyTenMinutes'     => $scheduled->everyTenMinutes(),
            $task->frequency === 'everyFifteenMinutes' => $scheduled->everyFifteenMinutes(),
            $task->frequency === 'everyThirtyMinutes'  => $scheduled->everyThirtyMinutes(),
            $task->frequency === 'weekly'              => $scheduled->weekly(),
            $task->frequency === 'monthly'             => $scheduled->monthly(),
            default                                    => $scheduled->hourly(),
        };

        // Prevent overlapping runs and ensure single-server execution
        $scheduled->withoutOverlapping()->onOneServer();

        $scheduled->after(function () use ($task) {
            $task->update([
                'last_run_at' => now(),
                'last_status' => 'success',
                'run_count'   => $task->run_count + 1,
                'next_run_at' => (new TaskSchedulerService())->calculateNextRun($task->frequency),
            ]);
            $task->logs()->create([
                'status'      => 'success',
                'started_at'  => now(),
                'finished_at' => now(),
            ]);
        });
    }
} catch (\Exception $e) {
    // Fallback: table doesn't exist yet (pre-migration)
    Schedule::command('bank:process-fd-maturity')->dailyAt('00:30')->withoutOverlapping()->onOneServer();
    Schedule::command('bank:process-loan-od-interest')->dailyAt('01:00')->withoutOverlapping()->onOneServer();
    Schedule::command('queue:work --stop-when-empty')->everyFiveMinutes()->withoutOverlapping();
}
