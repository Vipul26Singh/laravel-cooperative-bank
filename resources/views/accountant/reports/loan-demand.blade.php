@extends('layouts.app')
@section('title', 'Loan Demand')
@section('page-title', 'Loan Demand Collection Sheet')
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Demand Sheet</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>Loan #</th><th>Customer</th><th>Type</th><th>EMI</th><th>Outstanding</th><th>Last Payment</th></tr></thead>
            <tbody>
                @forelse($loans as $l)
                <tr>
                    <td>{{ $l->loan_number }}</td><td>{{ $l->customer?->full_name }}</td><td>{{ $l->loanType?->name }}</td>
                    <td class="text-right">{{ number_format($l->installment_amount, 2) }}</td>
                    <td class="text-right font-weight-bold">{{ number_format($l->outstanding_balance, 2) }}</td>
                    <td>{{ $l->transactions->first()?->payment_date?->format('d/m/Y') ?? 'Never' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No active loans.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
