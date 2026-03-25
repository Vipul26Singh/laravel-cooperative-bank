@extends('layouts.app')
@section('title', 'Account Details')
@section('page-title', 'Account #' . $bankAccount->account_number)
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card"><div class="card-header"><h3 class="card-title">Account Info</h3></div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Account #</th><td>{{ $bankAccount->account_number }}</td></tr>
                    <tr><th>Customer</th><td>{{ $bankAccount->customer?->full_name }}</td></tr>
                    <tr><th>Type</th><td>{{ $bankAccount->accountType?->name }}</td></tr>
                    <tr><th>Balance</th><td class="font-weight-bold">{{ number_format($bankAccount->balance, 2) }}</td></tr>
                    <tr><th>Opened</th><td>{{ $bankAccount->opening_date?->format('d M Y') }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $bankAccount->is_active ? 'success' : 'danger' }}">{{ $bankAccount->is_active ? 'Active' : 'Closed' }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card"><div class="card-header"><h3 class="card-title">Recent Transactions</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm text-nowrap">
                    <thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Balance</th></tr></thead>
                    <tbody>
                        @forelse($bankAccount->transactions->take(10) as $tx)
                        <tr><td>{{ $tx->transaction_date?->format('d/m/Y') }}</td><td>{{ $tx->transaction_type }}</td><td>{{ number_format($tx->amount, 2) }}</td><td>{{ number_format($tx->balance_after, 2) }}</td></tr>
                        @empty
                        <tr><td colspan="4" class="text-muted text-center">No transactions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
