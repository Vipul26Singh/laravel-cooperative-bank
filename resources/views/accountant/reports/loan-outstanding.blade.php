@extends('layouts.app')
@section('title', 'Loan Outstanding')
@section('page-title', 'Loan Outstanding Report')
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Active Loans</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>Loan #</th><th>Customer</th><th>Type</th><th>Amount</th><th>Outstanding</th><th>Rate</th><th>EMI</th></tr></thead>
            <tbody>
                @forelse($loans as $l)
                <tr>
                    <td>{{ $l->loan_number }}</td><td>{{ $l->customer?->full_name }}</td><td>{{ $l->loanType?->name }}</td>
                    <td class="text-right">{{ number_format($l->amount, 2) }}</td><td class="text-right font-weight-bold text-danger">{{ number_format($l->outstanding_balance, 2) }}</td>
                    <td>{{ $l->interest_rate }}%</td><td class="text-right">{{ number_format($l->installment_amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No active loans.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
