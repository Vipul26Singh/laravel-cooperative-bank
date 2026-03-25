@extends('layouts.app')
@section('title', 'Transactions')
@section('page-title', "Today's Transactions")
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Transaction List</h3>
        <div class="card-tools"><a href="{{ route('cashier.transactions.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Transaction</a></div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>Account #</th><th>Customer</th><th>Type</th><th>Amount</th><th>Balance After</th><th>Mode</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->account_number }}</td><td>{{ $tx->customer?->full_name }}</td>
                    <td><span class="badge badge-{{ $tx->transaction_type == 'Deposit' ? 'success' : 'danger' }}">{{ $tx->transaction_type }}</span></td>
                    <td class="text-right">{{ number_format($tx->amount, 2) }}</td><td class="text-right">{{ number_format($tx->balance_after, 2) }}</td>
                    <td>{{ ucfirst($tx->transaction_mode) }}</td><td>{{ $tx->transaction_date?->format('d/m/Y H:i') }}</td>
                    <td><a href="{{ route('cashier.transactions.show', $tx) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">No transactions today.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())<div class="card-footer">{{ $transactions->links() }}</div>@endif
</div>
@endsection
