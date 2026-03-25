@extends('layouts.app')
@section('title', 'Transaction Receipt')
@section('page-title', 'Transaction Receipt')
@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Transaction ID</th><td>{{ $transaction->id }}</td></tr>
            <tr><th>Account #</th><td>{{ $transaction->account_number }}</td></tr>
            <tr><th>Customer</th><td>{{ $transaction->customer?->full_name ?? '-' }}</td></tr>
            <tr><th>Type</th><td><span class="badge badge-{{ $transaction->transaction_type == 'Deposit' ? 'success' : 'danger' }}">{{ $transaction->transaction_type }}</span></td></tr>
            <tr><th>Amount</th><td class="font-weight-bold">{{ number_format($transaction->amount, 2) }}</td></tr>
            <tr><th>Balance After</th><td>{{ number_format($transaction->balance_after, 2) }}</td></tr>
            <tr><th>Mode</th><td>{{ ucfirst($transaction->transaction_mode) }}</td></tr>
            @if($transaction->cheque_number)<tr><th>Cheque #</th><td>{{ $transaction->cheque_number }}</td></tr>@endif
            <tr><th>Date</th><td>{{ $transaction->transaction_date?->format('d M Y H:i') }}</td></tr>
            <tr><th>Remarks</th><td>{{ $transaction->remarks ?? '-' }}</td></tr>
        </table>
    </div>
    <div class="card-footer"><a href="{{ route('cashier.transactions.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Back</a> <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Receipt</button></div>
</div>
@endsection
