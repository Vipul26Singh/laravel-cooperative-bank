@extends('layouts.app')

@section('title', 'Cashier Dashboard')
@section('page-title', 'Cashier Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['today_transactions'] ?? 0 }}</h3>
                <p>Today's Transactions</p>
            </div>
            <div class="icon"><i class="fas fa-exchange-alt"></i></div>
            <a href="{{ route('cashier.transactions.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['today_deposits'] ?? 0 }}</h3>
                <p>Today's Deposits</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
            <a href="{{ route('cashier.transactions.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['today_withdrawals'] ?? 0 }}</h3>
                <p>Today's Withdrawals</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
            <a href="{{ route('cashier.transactions.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['today_repayments'] ?? 0 }}</h3>
                <p>Today's Repayments</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            <a href="{{ route('cashier.loan-repayments.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Quick Links</h3></div>
            <div class="card-body">
                <a href="{{ route('cashier.transactions.create') }}" class="btn btn-outline-info btn-sm mr-2 mb-2"><i class="fas fa-plus"></i> New Transaction</a>
                <a href="{{ route('cashier.transactions.index') }}" class="btn btn-outline-success btn-sm mr-2 mb-2"><i class="fas fa-list"></i> All Transactions</a>
                <a href="{{ route('cashier.loan-repayments.create') }}" class="btn btn-outline-warning btn-sm mr-2 mb-2"><i class="fas fa-money-bill-wave"></i> Record Repayment</a>
                <a href="{{ route('cashier.loan-repayments.index') }}" class="btn btn-outline-danger btn-sm mr-2 mb-2"><i class="fas fa-list"></i> All Repayments</a>
            </div>
        </div>
    </div>
</div>
@endsection
