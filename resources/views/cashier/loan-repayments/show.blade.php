@extends('layouts.app')
@section('title', 'Repayment Receipt')
@section('page-title', 'Repayment Receipt')
@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Loan #</th><td>{{ $loanTransaction->loan?->loan_number }}</td></tr>
            <tr><th>Customer</th><td>{{ $loanTransaction->loan?->customer?->full_name }}</td></tr>
            <tr><th>Loan Type</th><td>{{ $loanTransaction->loan?->loanType?->name }}</td></tr>
            <tr><th>Amount Paid</th><td class="font-weight-bold">{{ number_format($loanTransaction->amount_paid, 2) }}</td></tr>
            <tr><th>Principal</th><td>{{ number_format($loanTransaction->principal_paid, 2) }}</td></tr>
            <tr><th>Interest</th><td>{{ number_format($loanTransaction->interest_paid, 2) }}</td></tr>
            <tr><th>Outstanding After</th><td>{{ number_format($loanTransaction->outstanding_balance_after, 2) }}</td></tr>
            <tr><th>Payment Date</th><td>{{ $loanTransaction->payment_date?->format('d M Y') }}</td></tr>
            <tr><th>Mode</th><td>{{ ucfirst($loanTransaction->transaction_mode) }}</td></tr>
        </table>
    </div>
    <div class="card-footer"><a href="{{ route('cashier.loan-repayments.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Back</a> <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print</button></div>
</div>
@endsection
