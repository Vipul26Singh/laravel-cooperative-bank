@extends('layouts.app')
@section('title', 'Transaction Statement')
@section('page-title', 'Transaction Statement')
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Statement</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline mb-3">
            <input type="text" name="account_number" class="form-control mr-2" placeholder="Account Number" value="{{ request('account_number') }}">
            <input type="date" name="from_date" class="form-control mr-2" value="{{ request('from_date') }}" required>
            <input type="date" name="to_date" class="form-control mr-2" value="{{ request('to_date') }}" required>
            <button class="btn btn-info"><i class="fas fa-search"></i> Generate</button>
        </form>
        @if(isset($transactions))
        <div class="table-responsive">
            <table class="table table-striped text-nowrap">
                <thead><tr><th>Date</th><th>Account #</th><th>Customer</th><th>Type</th><th>Amount</th><th>Balance</th></tr></thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr><td>{{ $tx->transaction_date?->format('d/m/Y') }}</td><td>{{ $tx->account_number }}</td><td>{{ $tx->customer?->full_name }}</td>
                        <td><span class="badge badge-{{ $tx->transaction_type == 'Deposit' ? 'success' : 'danger' }}">{{ $tx->transaction_type }}</span></td>
                        <td class="text-right">{{ number_format($tx->amount, 2) }}</td><td class="text-right">{{ number_format($tx->balance_after, 2) }}</td></tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">No transactions in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
