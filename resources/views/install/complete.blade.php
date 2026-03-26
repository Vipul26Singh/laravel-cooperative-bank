@extends('install.layout', ['step' => 5])
@section('content')
<div class="card-body text-center p-5">
    <div class="mb-3">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width:80px;height:80px;">
            <i class="fas fa-check-circle fa-3x text-success"></i>
        </span>
    </div>
    <h4 class="text-success">Installation Complete!</h4>
    <p class="text-muted">CoopBank ERP has been installed successfully. You can now log in with your admin credentials.</p>
    <a href="/login" class="btn btn-primary px-5 mt-2"><i class="fas fa-sign-in-alt me-2"></i>Go to Login</a>
</div>
@endsection
