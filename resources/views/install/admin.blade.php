@extends('install.layout', ['step' => 4])
@section('content')
<form action="{{ route('install.admin') }}" method="POST">
    @csrf
    <div class="card-body p-4">
        <h5 class="mb-3"><i class="fas fa-user-shield text-primary me-2"></i>Application & Admin Setup</h5>

        @if($errors->any())
            <div class="alert alert-danger">@foreach($errors->all() as $e)<p class="mb-0">{{ $e }}</p>@endforeach</div>
        @endif

        <h6 class="text-muted mt-3 mb-2">Application</h6>
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">App Name</label>
                <input type="text" name="app_name" class="form-control" value="{{ old('app_name', 'CoopBank ERP') }}" required>
            </div>
            <div class="col-6">
                <label class="form-label">App URL</label>
                <input type="url" name="app_url" class="form-control" value="{{ old('app_url', 'http://localhost:8000') }}" required>
            </div>
        </div>

        <h6 class="text-muted mt-4 mb-2">SuperAdmin Account</h6>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="admin_name" class="form-control" value="{{ old('admin_name', 'Super Administrator') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}" required placeholder="admin@yourbank.com">
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Password</label>
                <input type="password" name="admin_password" class="form-control" required minlength="8">
                <small class="text-muted">Minimum 8 characters</small>
            </div>
            <div class="col-6">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="admin_password_confirmation" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="{{ route('install.database') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
        <button type="submit" class="btn btn-primary">Next <i class="fas fa-arrow-right ms-1"></i></button>
    </div>
</form>
@endsection
