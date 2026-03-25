@extends('layouts.app')
@section('title', 'Loan Repayments')
@section('page-title', "Today's Loan Collections")
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Repayments</h3>
        <div class="card-tools"><a href="{{ route('cashier.loan-repayments.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Repayment</a></div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>Loan #</th><th>Customer</th><th>Amount Paid</th><th>Principal</th><th>Interest</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($collections as $c)
                <tr>
                    <td>{{ $c->loan?->loan_number }}</td><td>{{ $c->loan?->customer?->full_name }}</td>
                    <td class="text-right">{{ number_format($c->amount_paid, 2) }}</td>
                    <td class="text-right">{{ number_format($c->principal_paid, 2) }}</td>
                    <td class="text-right">{{ number_format($c->interest_paid, 2) }}</td>
                    <td>{{ $c->payment_date?->format('d/m/Y') }}</td>
                    <td><a href="{{ route('cashier.loan-repayments.show', $c) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No collections today.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($collections->hasPages())<div class="card-footer">{{ $collections->links() }}</div>@endif
</div>
@endsection
