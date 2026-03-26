@extends('layouts.app')
@section('title', 'Task Scheduler')
@section('page-title', 'Task Scheduler')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Scheduled Tasks</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Command</th>
                    <th>Frequency</th>
                    <th>Last Run</th>
                    <th>Status</th>
                    <th>Runs</th>
                    <th>Next Run</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr class="{{ !$task->is_active ? 'text-muted' : '' }}">
                    <td>
                        <strong>{{ $task->name }}</strong>
                        @if($task->description)
                            <br><small class="text-muted">{{ $task->description }}</small>
                        @endif
                    </td>
                    <td><code>{{ $task->command }}</code></td>
                    <td><span class="badge badge-info">{{ $task->frequency_label }}</span></td>
                    <td>
                        @if($task->last_run_at)
                            {{ $task->last_run_at->diffForHumans() }}
                            <br><small class="text-muted">{{ $task->last_run_at->format('d M Y H:i') }}</small>
                        @else
                            <span class="text-muted">Never</span>
                        @endif
                    </td>
                    <td>
                        @if($task->last_status === 'success')
                            <span class="badge badge-success"><i class="fas fa-check"></i> Success</span>
                        @elseif($task->last_status === 'failed')
                            <span class="badge badge-danger"><i class="fas fa-times"></i> Failed</span>
                        @elseif($task->last_status === 'running')
                            <span class="badge badge-warning"><i class="fas fa-spinner fa-spin"></i> Running</span>
                        @else
                            <span class="badge badge-secondary">Idle</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-success">{{ $task->run_count - $task->fail_count }}</span> /
                        <span class="text-danger">{{ $task->fail_count }}</span>
                    </td>
                    <td>
                        @if($task->is_active && $task->next_run_at)
                            {{ $task->next_run_at->diffForHumans() }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        {{-- Toggle Active --}}
                        <form action="{{ route('superadmin.task-scheduler.toggle', $task) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-xs {{ $task->is_active ? 'btn-warning' : 'btn-success' }}" title="{{ $task->is_active ? 'Disable' : 'Enable' }}">
                                <i class="fas {{ $task->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                            </button>
                        </form>
                        {{-- Edit Schedule --}}
                        <a href="{{ route('superadmin.task-scheduler.edit', $task) }}" class="btn btn-xs btn-info" title="Edit Schedule"><i class="fas fa-edit"></i></a>
                        {{-- Run Now --}}
                        @if($task->is_active)
                        <form action="{{ route('superadmin.task-scheduler.run', $task) }}" method="POST" class="d-inline" data-confirm="Run this task now?" data-confirm-yes="Yes, run it">
                            @csrf
                            <button class="btn btn-xs btn-primary" title="Run Now"><i class="fas fa-play-circle"></i></button>
                        </form>
                        @endif
                        {{-- Logs --}}
                        <a href="{{ route('superadmin.task-scheduler.logs', $task) }}" class="btn btn-xs btn-default" title="View Logs">
                            <i class="fas fa-list"></i> {{ $task->logs_count }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">No scheduled tasks configured.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($tasks->isEmpty())
<div class="alert alert-info">
    <i class="fas fa-info-circle mr-2"></i>
    No tasks found. Run <code>php artisan db:seed --class=ScheduledTaskSeeder</code> to load the default tasks.
</div>
@endif
@endsection
