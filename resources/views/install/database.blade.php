@extends('install.layout', ['step' => 3])
@section('content')
<form action="{{ route('install.database') }}" method="POST">
    @csrf
    <div class="card-body p-4">
        <h5 class="mb-3"><i class="fas fa-database text-primary me-2"></i>Database Configuration</h5>

        @if($errors->any())
            <div class="alert alert-danger">@foreach($errors->all() as $e)<p class="mb-0">{{ $e }}</p>@endforeach</div>
        @endif

        <div class="mb-3">
            <label class="form-label fw-bold">Database Type</label>
            <select name="db_connection" id="dbType" class="form-select" required>
                <option value="sqlite" {{ old('db_connection') == 'sqlite' ? 'selected' : '' }}>SQLite (Easiest — no setup needed)</option>
                <option value="mysql" {{ old('db_connection') == 'mysql' ? 'selected' : '' }}>MySQL</option>
                <option value="pgsql" {{ old('db_connection') == 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
            </select>
        </div>

        <div id="dbFields" style="display:none;">
            <div class="row mb-3">
                <div class="col-8">
                    <label class="form-label">Host</label>
                    <input type="text" name="db_host" class="form-control" value="{{ old('db_host', '127.0.0.1') }}">
                </div>
                <div class="col-4">
                    <label class="form-label">Port</label>
                    <input type="text" name="db_port" class="form-control" value="{{ old('db_port', '3306') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Database Name</label>
                <input type="text" name="db_database" class="form-control" value="{{ old('db_database', 'coopbank') }}">
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="db_username" class="form-control" value="{{ old('db_username', 'root') }}">
                </div>
                <div class="col-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="db_password" class="form-control" value="{{ old('db_password') }}">
                </div>
            </div>
        </div>

        <div id="sqliteInfo" class="alert alert-info">
            <i class="fas fa-info-circle me-1"></i> SQLite requires no configuration. A database file will be created automatically.
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="{{ route('install.requirements') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
        <button type="submit" class="btn btn-primary">Next <i class="fas fa-arrow-right ms-1"></i></button>
    </div>
</form>
<script>
document.getElementById('dbType').addEventListener('change', function() {
    var isSqlite = this.value === 'sqlite';
    document.getElementById('dbFields').style.display = isSqlite ? 'none' : 'block';
    document.getElementById('sqliteInfo').style.display = isSqlite ? 'block' : 'none';
});
document.getElementById('dbType').dispatchEvent(new Event('change'));
</script>
@endsection
