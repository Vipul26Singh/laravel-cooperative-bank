@extends('layouts.app')

@section('title', 'SuperAdmin Dashboard')
@section('page-title', 'SuperAdmin Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_customers'] ?? 0 }}</h3>
                <p>Total Customers</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['total_branches'] ?? 0 }}</h3>
                <p>Branches</p>
            </div>
            <div class="icon"><i class="fas fa-code-branch"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['total_employees'] ?? 0 }}</h3>
                <p>Employees</p>
            </div>
            <div class="icon"><i class="fas fa-user-tie"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['total_loans'] ?? 0 }}</h3>
                <p>Active Loans</p>
            </div>
            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $stats['total_accounts'] ?? 0 }}</h3>
                <p>Bank Accounts</p>
            </div>
            <div class="icon"><i class="fas fa-university"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $stats['total_fd_accounts'] ?? 0 }}</h3>
                <p>FD Accounts</p>
            </div>
            <div class="icon"><i class="fas fa-piggy-bank"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['pending_loan_applications'] ?? 0 }}</h3>
                <p>Pending Loan Apps</p>
            </div>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['pending_customers'] ?? 0 }}</h3>
                <p>Pending Approvals</p>
            </div>
            <div class="icon"><i class="fas fa-user-clock"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Quick Links</h3></div>
            <div class="card-body">
                <a href="{{ route('superadmin.branches.index') }}" class="btn btn-outline-primary btn-sm mr-2 mb-2"><i class="fas fa-code-branch"></i> Manage Branches</a>
                <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-success btn-sm mr-2 mb-2"><i class="fas fa-users"></i> Manage Users</a>
                <a href="{{ route('superadmin.loan-types.index') }}" class="btn btn-outline-warning btn-sm mr-2 mb-2"><i class="fas fa-hand-holding-usd"></i> Loan Types</a>
                <a href="{{ route('superadmin.fd-setups.index') }}" class="btn btn-outline-info btn-sm mr-2 mb-2"><i class="fas fa-piggy-bank"></i> FD Setups</a>
                <a href="{{ route('superadmin.account-types.index') }}" class="btn btn-outline-secondary btn-sm mr-2 mb-2"><i class="fas fa-wallet"></i> Account Types</a>
                <a href="{{ route('superadmin.company-setup.show') }}" class="btn btn-outline-danger btn-sm mr-2 mb-2"><i class="fas fa-cog"></i> Company Setup</a>
            </div>
        </div>
    </div>
</div>
@endsection
