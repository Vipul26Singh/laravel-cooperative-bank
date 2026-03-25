@extends('layouts.app')
@section('title', 'Loans')
@section('page-title', 'Loans')
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Branch Loans</h3>
        <div class="card-tools"><a href="{{ route('manager.loans.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Disburse Loan</a></div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>Loan #</th><th>Customer</th><th>Type</th><th>Amount</th><th>Outstanding</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($loans as $l)
                <tr>
                    <td>{{ $l->loan_number }}</td><td>{{ $l->customer?->full_name }}</td><td>{{ $l->loanType?->name }}</td>
                    <td class="text-right">{{ number_format($l->amount, 2) }}</td><td class="text-right">{{ number_format($l->outstanding_balance, 2) }}</td>
                    <td><span class="badge badge-{{ $l->status == 'active' ? 'success' : ($l->status == 'closed' ? 'secondary' : 'warning') }}">{{ ucfirst($l->status) }}</span></td>
                    <td><a href="{{ route('manager.loans.show', $l) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a> <a href="{{ route('manager.loans.schedule', $l) }}" class="btn btn-xs btn-outline-primary"><i class="fas fa-calendar"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No loans found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($loans->hasPages())<div class="card-footer">{{ $loans->links() }}</div>@endif
</div>
@endsection
