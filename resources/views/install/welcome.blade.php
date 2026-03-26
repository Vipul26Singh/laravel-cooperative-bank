@extends('install.layout', ['step' => 1])
@section('content')
<div class="card-body text-center p-5">
    <i class="fas fa-cogs fa-3x text-primary mb-3"></i>
    <h4>Welcome to CoopBank ERP Installer</h4>
    <p class="text-muted">This wizard will guide you through the setup process. You'll need:</p>
    <ul class="text-start text-muted" style="max-width:320px;margin:0 auto;">
        <li>Database credentials (MySQL/PostgreSQL) or SQLite</li>
        <li>Admin user name, email, and password</li>
        <li>Your application URL</li>
    </ul>
    <a href="{{ route('install.requirements') }}" class="btn btn-primary mt-4 px-5"><i class="fas fa-arrow-right me-2"></i>Let's Start</a>
</div>
@endsection
