@extends('layouts.app')
@section('title', 'Queue Monitor')
@section('page-title', 'Queue Monitor')
@section('content')

{{-- Stats Cards --}}
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3>{{ $stats['pending'] }}</h3><p>Pending Jobs</p></div>
            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-danger">
            <div class="inner"><h3>{{ $stats['failed'] }}</h3><p>Failed Jobs</p></div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ $stats['queues']->count() }}</h3><p>Active Queues</p></div>
            <div class="icon"><i class="fas fa-layer-group"></i></div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="mb-3">
    <form action="{{ route('superadmin.queue-monitor.process') }}" method="POST" class="d-inline" data-confirm="Process all pending jobs now?" data-confirm-yes="Yes, process">
        @csrf
        <button class="btn btn-primary btn-sm"><i class="fas fa-play mr-1"></i>Process Queue Now</button>
    </form>
    @if($stats['failed'] > 0)
    <form action="{{ route('superadmin.queue-monitor.retry-all') }}" method="POST" class="d-inline" data-confirm="Retry all {{ $stats['failed'] }} failed jobs?" data-confirm-yes="Yes, retry all">
        @csrf
        <button class="btn btn-warning btn-sm"><i class="fas fa-redo mr-1"></i>Retry All Failed</button>
    </form>
    <form action="{{ route('superadmin.queue-monitor.flush') }}" method="POST" class="d-inline" data-confirm="Delete all failed jobs permanently?" data-confirm-text="This cannot be undone." data-confirm-yes="Yes, delete all">
        @csrf @method('DELETE')
        <button class="btn btn-danger btn-sm"><i class="fas fa-trash mr-1"></i>Clear All Failed</button>
    </form>
    @endif
</div>

{{-- Pending Jobs --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-hourglass-half mr-2"></i>Pending Jobs ({{ $stats['pending'] }})</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>ID</th><th>Job</th><th>Queue</th><th>Attempts</th><th>Created</th></tr></thead>
            <tbody>
                @forelse($pendingJobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td><code>{{ class_basename($job->display_name) }}</code></td>
                    <td><span class="badge badge-info">{{ $job->queue }}</span></td>
                    <td>{{ $job->attempts }}</td>
                    <td>{{ $job->created->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No pending jobs. Queue is empty.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Failed Jobs --}}
<div class="card card-danger card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Failed Jobs ({{ $stats['failed'] }})</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead><tr><th>UUID</th><th>Job</th><th>Queue</th><th>Failed At</th><th>Exception</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($failedJobs as $job)
                <tr>
                    <td><small>{{ Str::limit($job->uuid, 8, '...') }}</small></td>
                    <td><code>{{ class_basename($job->display_name) }}</code></td>
                    <td><span class="badge badge-danger">{{ $job->queue }}</span></td>
                    <td>{{ $job->failed->diffForHumans() }}<br><small class="text-muted">{{ $job->failed->format('d M Y H:i') }}</small></td>
                    <td><small class="text-danger" title="{{ $job->exception }}">{{ Str::limit($job->exception, 100) }}</small></td>
                    <td class="text-nowrap">
                        <form action="{{ route('superadmin.queue-monitor.retry', $job->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-xs btn-warning" title="Retry"><i class="fas fa-redo"></i></button>
                        </form>
                        <form action="{{ route('superadmin.queue-monitor.forget', $job->uuid) }}" method="POST" class="d-inline" data-confirm="Delete this failed job?">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No failed jobs.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Queue Breakdown --}}
@if($stats['queues']->isNotEmpty())
<div class="card">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-layer-group mr-2"></i>Jobs by Queue</h3></div>
    <div class="card-body">
        @foreach($stats['queues'] as $queue => $count)
            <span class="badge badge-primary mr-2 p-2">{{ $queue }}: {{ $count }}</span>
        @endforeach
    </div>
</div>
@endif
@endsection
