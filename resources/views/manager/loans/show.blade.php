@extends('layouts.app')
@section('title', 'Loan Details')
@section('page-title', 'Loan #' . $loan->loan_number)
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card"><div class="card-header"><h3 class="card-title">Loan Info</h3></div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Loan #</th><td>{{ $loan->loan_number }}</td></tr>
                    <tr><th>Customer</th><td>{{ $loan->customer?->full_name }}</td></tr>
                    <tr><th>Type</th><td>{{ $loan->loanType?->name }}</td></tr>
                    <tr><th>Amount</th><td>{{ number_format($loan->amount, 2) }}</td></tr>
                    <tr><th>Interest Rate</th><td>{{ $loan->interest_rate }}%</td></tr>
                    <tr><th>EMI</th><td>{{ number_format($loan->installment_amount, 2) }}</td></tr>
                    <tr><th>Outstanding</th><td class="font-weight-bold text-danger">{{ number_format($loan->outstanding_balance, 2) }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $loan->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($loan->status) }}</span></td></tr>
                </table>
                <a href="{{ route('manager.loans.schedule', $loan) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-calendar"></i> View Schedule</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card"><div class="card-header"><h3 class="card-title">Repayment History</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm text-nowrap">
                    <thead><tr><th>Date</th><th>Paid</th><th>Principal</th><th>Interest</th><th>Balance</th></tr></thead>
                    <tbody>
                        @forelse(($loan->transactions ?? collect()) as $tx)
                        <tr><td>{{ $tx->payment_date?->format('d/m/Y') }}</td><td>{{ number_format($tx->amount_paid, 2) }}</td><td>{{ number_format($tx->principal_paid, 2) }}</td><td>{{ number_format($tx->interest_paid, 2) }}</td><td>{{ number_format($tx->outstanding_balance_after, 2) }}</td></tr>
                        @empty
                        <tr><td colspan="5" class="text-muted text-center">No repayments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
