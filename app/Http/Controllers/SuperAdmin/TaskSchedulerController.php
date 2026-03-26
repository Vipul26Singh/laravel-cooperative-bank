<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\{ScheduledTask, TaskRunLog};
use App\Services\TaskSchedulerService;
use Illuminate\Http\Request;

class TaskSchedulerController extends Controller
{
    public function __construct(private TaskSchedulerService $service) {}

    public function index()
    {
        $tasks = ScheduledTask::withCount('logs')->latest('updated_at')->get();
        return view('superadmin.task-scheduler.index', compact('tasks'));
    }

    public function toggle(ScheduledTask $scheduledTask)
    {
        $scheduledTask->update(['is_active' => !$scheduledTask->is_active]);
        $status = $scheduledTask->is_active ? 'enabled' : 'disabled';
        return redirect()->route('superadmin.task-scheduler.index')
            ->with('success', "Task \"{$scheduledTask->name}\" {$status}.");
    }

    public function run(ScheduledTask $scheduledTask)
    {
        $log = $this->service->runNow($scheduledTask, auth()->id());
        $message = $log->status === 'success'
            ? "Task \"{$scheduledTask->name}\" completed successfully ({$log->duration_ms}ms)."
            : "Task \"{$scheduledTask->name}\" failed: {$log->output}";

        return redirect()->route('superadmin.task-scheduler.index')
            ->with($log->status === 'success' ? 'success' : 'error', $message);
    }

    public function edit(ScheduledTask $scheduledTask)
    {
        $frequencies = [
            'everyFiveMinutes'    => 'Every 5 Minutes',
            'everyTenMinutes'     => 'Every 10 Minutes',
            'everyFifteenMinutes' => 'Every 15 Minutes',
            'everyThirtyMinutes'  => 'Every 30 Minutes',
            'hourly'              => 'Every Hour',
            'daily'               => 'Daily at Midnight',
            'dailyAt'             => 'Daily at Specific Time',
            'weekly'              => 'Weekly',
            'monthly'             => 'Monthly',
        ];
        return view('superadmin.task-scheduler.edit', compact('scheduledTask', 'frequencies'));
    }

    public function update(Request $request, ScheduledTask $scheduledTask)
    {
        $request->validate([
            'frequency_type' => 'required|string',
            'run_time'       => 'nullable|date_format:H:i',
        ]);

        $frequency = $request->frequency_type;
        if ($frequency === 'dailyAt' && $request->filled('run_time')) {
            $frequency = 'dailyAt:' . $request->run_time;
        }

        $scheduledTask->update([
            'frequency'   => $frequency,
            'next_run_at' => $this->service->calculateNextRun($frequency),
        ]);

        return redirect()->route('superadmin.task-scheduler.index')
            ->with('success', "Schedule for \"{$scheduledTask->name}\" updated.");
    }

    public function logs(ScheduledTask $scheduledTask)
    {
        $task = $scheduledTask;
        $logs = $scheduledTask->logs()->with('triggeredBy')->latest()->paginate(30);
        return view('superadmin.task-scheduler.logs', compact('task', 'logs'));
    }
}
