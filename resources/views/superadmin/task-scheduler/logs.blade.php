@extends('layouts.app')
@section('title', 'Task Logs — ' . $task->name)
@section('page-title', 'Task Run History')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history mr-2"></i>{{ $task->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('superadmin.task-scheduler.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Back to Tasks</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="p-3">
            <table class="table table-sm table-bordered mb-0" style="max-width:500px;">
                <tr><th>Command</th><td><code>{{ $task->command }}</code></td></tr>
                <tr><th>Frequency</th><td>{{ $task->frequency_label }}</td></tr>
                <tr><th>Total Runs</th><td>{{ $task->run_count }}</td></tr>
                <tr><th>Failures</th><td class="{{ $task->fail_count > 0 ? 'text-danger font-weight-bold' : '' }}">{{ $task->fail_count }}</td></tr>
                <tr><th>Status</th><td><span class="badge badge-{{ $task->is_active ? 'success' : 'secondary' }}">{{ $task->is_active ? 'Active' : 'Disabled' }}</span></td></tr>
            </table>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr><th>Time</th><th>Status</th><th>Duration</th><th>Triggered By</th><th>Output</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        {{ $log->started_at->format('d M Y H:i:s') }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $log->status === 'success' ? 'success' : 'danger' }}">{{ ucfirst($log->status) }}</span>
                    </td>
                    <td>{{ number_format($log->duration_ms) }}ms</td>
                    <td>{{ $log->triggeredBy?->name ?? 'Scheduler' }}</td>
                    <td>
                        @if($log->output)
                            <code class="text-sm" title="{{ $log->output }}">{{ \Illuminate\Support\Str::limit($log->output, 80) }}</code>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No runs recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
