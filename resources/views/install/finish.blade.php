@extends('install.layout', ['step' => 5])
@section('content')
<div class="card-body p-4">
    <h5 class="mb-3"><i class="fas fa-check-double text-primary me-2"></i>Review & Install</h5>

    <table class="table table-sm">
        <tr><th>Database</th><td><code>{{ $db['db_connection'] }}</code> {{ $db['db_connection'] !== 'sqlite' ? '@ ' . ($db['db_host'] ?? '') . ':' . ($db['db_port'] ?? '') : '' }}</td></tr>
        @if($db['db_connection'] !== 'sqlite')
        <tr><th>DB Name</th><td><code>{{ $db['db_database'] ?? '' }}</code></td></tr>
        @endif
        <tr><th>App Name</th><td>{{ $admin['app_name'] ?? '' }}</td></tr>
        <tr><th>App URL</th><td><code>{{ $admin['app_url'] ?? '' }}</code></td></tr>
        <tr><th>Admin</th><td>{{ $admin['admin_name'] ?? '' }} ({{ $admin['admin_email'] ?? '' }})</td></tr>
    </table>

    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-1"></i>
        This will create the <code>.env</code> file, run database migrations, and create the admin user. This action cannot be undone.
    </div>
</div>
<div class="card-footer d-flex justify-content-between">
    <a href="{{ route('install.admin') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <form action="{{ route('install.run') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success" onclick="this.disabled=true;this.innerHTML='<i class=\'fas fa-spinner fa-spin me-1\'></i>Installing...';this.form.submit();">
            <i class="fas fa-rocket me-1"></i>Install Now
        </button>
    </form>
</div>
@endsection
