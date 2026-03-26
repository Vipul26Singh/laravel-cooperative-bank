@extends('install.layout', ['step' => 5])
@section('content')
<div class="card-body p-4">

    <div class="text-center mb-4">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width:72px;height:72px;">
            <i class="fas fa-check-circle fa-3x text-success"></i>
        </span>
        <h4 class="text-success mt-2">Installation Complete!</h4>
    </div>

    {{-- Setup Steps Summary --}}
    @if(session('steps'))
    <h6 class="fw-bold mb-2"><i class="fas fa-list-check me-1"></i> Setup Summary</h6>
    <table class="table table-sm table-bordered mb-4">
        @foreach(session('steps') as $step)
        <tr>
            <td>
                @if($step['status'] === 'success')
                    <i class="fas fa-check-circle text-success"></i>
                @elseif($step['status'] === 'skipped')
                    <i class="fas fa-minus-circle text-warning"></i>
                @else
                    <i class="fas fa-times-circle text-danger"></i>
                @endif
                {{ $step['name'] }}
                @if(!empty($step['note']))
                    <small class="text-muted ms-1">({{ $step['note'] }})</small>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    {{-- Login Info --}}
    <div class="alert alert-success">
        <strong><i class="fas fa-user-shield me-1"></i> Admin Login:</strong><br>
        Email: <code>{{ session('admin_email', 'your admin email') }}</code><br>
        Password: <em>the password you set during setup</em>
    </div>

    {{-- Post-install Commands --}}
    <h6 class="fw-bold mb-2"><i class="fas fa-terminal me-1"></i> Post-Install (for standalone servers)</h6>
    <table class="table table-sm table-bordered">
        <tr>
            <td><strong>Queue Worker</strong><br><small class="text-muted">Process background jobs (emails, notifications, audit)</small></td>
            <td><pre class="bg-dark text-white p-2 rounded mb-0" style="font-size:0.78rem;">php artisan queue:work</pre></td>
        </tr>
        <tr>
            <td><strong>Task Scheduler</strong><br><small class="text-muted">FD maturity, OD interest, EMI reminders, daily reports</small></td>
            <td><pre class="bg-dark text-white p-2 rounded mb-0" style="font-size:0.78rem;">* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1</pre></td>
        </tr>
    </table>
    <small class="text-muted"><i class="fab fa-docker me-1"></i>If using Docker, both are already running automatically via Supervisor.</small>

    <div class="text-center mt-4">
        <a href="/login" class="btn btn-primary px-5"><i class="fas fa-sign-in-alt me-2"></i>Go to Login</a>
    </div>

</div>
@endsection
