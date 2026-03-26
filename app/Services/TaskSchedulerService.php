<?php

namespace App\Services;

use App\Models\{ScheduledTask, TaskRunLog};
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class TaskSchedulerService
{
    /**
     * Run a task immediately (manual trigger from UI).
     */
    public function runNow(ScheduledTask $task, ?int $triggeredBy = null): TaskRunLog
    {
        $startedAt = now();
        $start = microtime(true);

        try {
            $task->update(['last_status' => 'running']);

            $exitCode = Artisan::call($task->command);
            $output = Artisan::output();

            $durationMs = (int) ((microtime(true) - $start) * 1000);
            $status = $exitCode === 0 ? 'success' : 'failed';

            $task->update([
                'last_run_at' => $startedAt,
                'last_status' => $status,
                'last_output' => trim($output),
                'next_run_at' => $this->calculateNextRun($task->frequency),
                'run_count'   => $task->run_count + 1,
                'fail_count'  => $status === 'failed' ? $task->fail_count + 1 : $task->fail_count,
            ]);

            return $task->logs()->create([
                'status'       => $status,
                'output'       => trim($output),
                'duration_ms'  => $durationMs,
                'started_at'   => $startedAt,
                'finished_at'  => now(),
                'triggered_by' => $triggeredBy,
            ]);
        } catch (\Throwable $e) {
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            $task->update([
                'last_run_at' => $startedAt,
                'last_status' => 'failed',
                'last_output' => $e->getMessage(),
                'run_count'   => $task->run_count + 1,
                'fail_count'  => $task->fail_count + 1,
            ]);

            return $task->logs()->create([
                'status'       => 'failed',
                'output'       => $e->getMessage(),
                'duration_ms'  => $durationMs,
                'started_at'   => $startedAt,
                'finished_at'  => now(),
                'triggered_by' => $triggeredBy,
            ]);
        }
    }

    /**
     * Seed the default scheduled tasks into the DB.
     */
    public function seedDefaults(): void
    {
        $defaults = [
            [
                'name'        => 'Process FD Maturity',
                'command'     => 'bank:process-fd-maturity',
                'frequency'   => 'dailyAt:00:30',
                'description' => 'Check and mark matured FD accounts, fire maturity notifications',
            ],
            [
                'name'        => 'Process Loan OD Interest',
                'command'     => 'bank:process-loan-od-interest',
                'frequency'   => 'dailyAt:01:00',
                'description' => 'Calculate and post overdraft interest on overdue loan accounts',
            ],
            [
                'name'        => 'Drain Job Queue',
                'command'     => 'queue:work --stop-when-empty',
                'frequency'   => 'everyFiveMinutes',
                'description' => 'Process pending background jobs (emails, SMS, audit logs)',
            ],
            [
                'name'        => 'Send Overdue EMI Reminders',
                'command'     => 'bank:send-overdue-reminders',
                'frequency'   => 'dailyAt:09:00',
                'description' => 'Email reminders to customers with overdue loan EMIs',
            ],
            [
                'name'        => 'Send Upcoming EMI Alerts',
                'command'     => 'bank:send-upcoming-emi-alerts',
                'frequency'   => 'dailyAt:08:00',
                'description' => 'Alert customers about EMIs due in the next 3 days',
            ],
            [
                'name'        => 'Clean Old Audit Logs',
                'command'     => 'bank:clean-audit-logs',
                'frequency'   => 'weekly',
                'description' => 'Delete audit log entries older than 90 days',
            ],
            [
                'name'        => 'Daily Transaction Report',
                'command'     => 'bank:daily-report',
                'frequency'   => 'dailyAt:23:00',
                'description' => 'Generate end-of-day transaction summary for all branches',
            ],
        ];

        foreach ($defaults as $task) {
            ScheduledTask::firstOrCreate(
                ['command' => $task['command']],
                array_merge($task, [
                    'is_active'   => true,
                    'next_run_at' => $this->calculateNextRun($task['frequency']),
                ])
            );
        }
    }

    /**
     * Calculate the next run time based on frequency string.
     */
    public function calculateNextRun(string $frequency): Carbon
    {
        return match (true) {
            str_starts_with($frequency, 'dailyAt:') => Carbon::tomorrow()->setTimeFromTimeString(str_replace('dailyAt:', '', $frequency)),
            $frequency === 'daily'                   => Carbon::tomorrow(),
            $frequency === 'hourly'                  => now()->addHour()->startOfHour(),
            $frequency === 'everyFiveMinutes'        => now()->addMinutes(5),
            $frequency === 'everyTenMinutes'         => now()->addMinutes(10),
            $frequency === 'everyFifteenMinutes'     => now()->addMinutes(15),
            $frequency === 'everyThirtyMinutes'      => now()->addMinutes(30),
            $frequency === 'weekly'                  => now()->addWeek()->startOfWeek(),
            $frequency === 'monthly'                 => now()->addMonth()->startOfMonth(),
            default                                  => now()->addHour(),
        };
    }
}
