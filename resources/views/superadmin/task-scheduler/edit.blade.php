@extends('layouts.app')
@section('title', 'Edit Schedule — ' . $scheduledTask->name)
@section('page-title', 'Edit Task Schedule')
@section('content')
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>{{ $scheduledTask->name }}</h3>
    </div>
    <form action="{{ route('superadmin.task-scheduler.update', $scheduledTask) }}" method="POST" data-validate>
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-sm mb-3">
                        <tr><th>Command</th><td><code>{{ $scheduledTask->command }}</code></td></tr>
                        <tr><th>Description</th><td>{{ $scheduledTask->description ?? '-' }}</td></tr>
                        <tr><th>Current Schedule</th><td><span class="badge badge-info">{{ $scheduledTask->frequency_label }}</span></td></tr>
                    </table>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">@foreach($errors->all() as $e)<p class="mb-0">{{ $e }}</p>@endforeach</div>
            @endif

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Frequency <span class="text-danger">*</span></label>
                        <select name="frequency_type" id="frequencyType" class="form-control" required aria-required="true">
                            @php
                                $currentType = str_starts_with($scheduledTask->frequency, 'dailyAt:') ? 'dailyAt' : $scheduledTask->frequency;
                            @endphp
                            @foreach($frequencies as $value => $label)
                                <option value="{{ $value }}" {{ $currentType === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3" id="timeGroup" style="display:none;">
                    <div class="form-group">
                        <label>Run Time <span class="text-danger">*</span></label>
                        @php
                            $currentTime = str_starts_with($scheduledTask->frequency, 'dailyAt:')
                                ? str_replace('dailyAt:', '', $scheduledTask->frequency) : '00:00';
                        @endphp
                        <input type="time" name="run_time" class="form-control" value="{{ old('run_time', $currentTime) }}" aria-label="Run time">
                        <small class="form-text">24-hour format (server timezone)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update Schedule</button>
            <a href="{{ route('superadmin.task-scheduler.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(function() {
    function toggleTime() {
        $('#timeGroup').toggle($('#frequencyType').val() === 'dailyAt');
    }
    $('#frequencyType').on('change', toggleTime);
    toggleTime();
});
</script>
@endpush
@endsection
