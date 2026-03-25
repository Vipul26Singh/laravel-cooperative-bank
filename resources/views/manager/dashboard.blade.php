@extends('layouts.app')

@section('title', 'Manager Dashboard')
@section('page-title', 'Manager Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_customers'] ?? 0 }}</h3>
                <p>Total Customers</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('manager.customers.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['pending_customers'] ?? 0 }}</h3>
                <p>Pending Approvals</p>
            </div>
            <div class="icon"><i class="fas fa-user-clock"></i></div>
            <a href="{{ route('manager.customers.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['total_accounts'] ?? 0 }}</h3>
                <p>Bank Accounts</p>
            </div>
            <div class="icon"><i class="fas fa-university"></i></div>
            <a href="{{ route('manager.bank-accounts.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['active_loans'] ?? 0 }}</h3>
                <p>Active Loans</p>
            </div>
            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
            <a href="{{ route('manager.loans.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $stats['total_fd_accounts'] ?? 0 }}</h3>
                <p>FD Accounts</p>
            </div>
            <div class="icon"><i class="fas fa-piggy-bank"></i></div>
            <a href="{{ route('manager.fd-accounts.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $stats['pending_loan_applications'] ?? 0 }}</h3>
                <p>Pending Loan Apps</p>
            </div>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
            <a href="{{ route('manager.loan-applications.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Quick Links</h3></div>
            <div class="card-body">
                <a href="{{ route('manager.customers.index') }}" class="btn btn-outline-info btn-sm mr-2 mb-2"><i class="fas fa-users"></i> Customers</a>
                <a href="{{ route('manager.bank-accounts.create') }}" class="btn btn-outline-success btn-sm mr-2 mb-2"><i class="fas fa-plus"></i> Open Account</a>
                <a href="{{ route('manager.loans.create') }}" class="btn btn-outline-warning btn-sm mr-2 mb-2"><i class="fas fa-hand-holding-usd"></i> Disburse Loan</a>
                <a href="{{ route('manager.loan-applications.index') }}" class="btn btn-outline-danger btn-sm mr-2 mb-2"><i class="fas fa-file-alt"></i> Loan Applications</a>
                <a href="{{ route('manager.fd-accounts.create') }}" class="btn btn-outline-primary btn-sm mr-2 mb-2"><i class="fas fa-piggy-bank"></i> Open FD</a>
            </div>
        </div>
    </div>
</div>
@endsection
