@extends('layouts.app')

@section('title', 'Accountant Dashboard')
@section('page-title', 'Accountant Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_loan_outstanding'] ?? '0.00' }}</h3>
                <p>Total Loan Outstanding</p>
            </div>
            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
            <a href="{{ route('accountant.reports.loan-outstanding') }}" class="small-box-footer">View Report <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['today_collections'] ?? '0.00' }}</h3>
                <p>Today's Collections</p>
            </div>
            <div class="icon"><i class="fas fa-coins"></i></div>
            <a href="{{ route('accountant.reports.transaction-statement') }}" class="small-box-footer">View Report <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['overdue_loans'] ?? 0 }}</h3>
                <p>Overdue Loans</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <a href="{{ route('accountant.reports.loan-demand') }}" class="small-box-footer">View Report <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['total_transactions_today'] ?? 0 }}</h3>
                <p>Transactions Today</p>
            </div>
            <div class="icon"><i class="fas fa-exchange-alt"></i></div>
            <a href="{{ route('accountant.reports.transaction-statement') }}" class="small-box-footer">View Report <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Reports</h3></div>
            <div class="card-body">
                <a href="{{ route('accountant.reports.loan-outstanding') }}" class="btn btn-outline-info btn-sm mr-2 mb-2"><i class="fas fa-chart-bar"></i> Loan Outstanding</a>
                <a href="{{ route('accountant.reports.transaction-statement') }}" class="btn btn-outline-success btn-sm mr-2 mb-2"><i class="fas fa-file-invoice"></i> Transaction Statement</a>
                <a href="{{ route('accountant.reports.loan-demand') }}" class="btn btn-outline-warning btn-sm mr-2 mb-2"><i class="fas fa-file-invoice-dollar"></i> Loan Demand</a>
            </div>
        </div>
    </div>
</div>
@endsection
