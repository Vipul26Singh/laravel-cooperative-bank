@extends('layouts.app')

@section('title', 'Clerk Dashboard')
@section('page-title', 'Clerk Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_customers'] ?? 0 }}</h3>
                <p>Total Customers</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('clerk.customers.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['pending_customers'] ?? 0 }}</h3>
                <p>Pending Approvals</p>
            </div>
            <div class="icon"><i class="fas fa-user-clock"></i></div>
            <a href="{{ route('clerk.customers.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['total_loan_applications'] ?? 0 }}</h3>
                <p>Loan Applications</p>
            </div>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
            <a href="{{ route('clerk.loan-applications.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['today_registrations'] ?? 0 }}</h3>
                <p>Today's Registrations</p>
            </div>
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <a href="{{ route('clerk.customers.create') }}" class="small-box-footer">Add New <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Quick Links</h3></div>
            <div class="card-body">
                <a href="{{ route('clerk.customers.create') }}" class="btn btn-outline-info btn-sm mr-2 mb-2"><i class="fas fa-user-plus"></i> New Customer</a>
                <a href="{{ route('clerk.customers.index') }}" class="btn btn-outline-success btn-sm mr-2 mb-2"><i class="fas fa-users"></i> View Customers</a>
                <a href="{{ route('clerk.loan-applications.create') }}" class="btn btn-outline-warning btn-sm mr-2 mb-2"><i class="fas fa-file-plus"></i> New Loan Application</a>
                <a href="{{ route('clerk.loan-applications.index') }}" class="btn btn-outline-danger btn-sm mr-2 mb-2"><i class="fas fa-list"></i> Loan Applications</a>
            </div>
        </div>
    </div>
</div>
@endsection
