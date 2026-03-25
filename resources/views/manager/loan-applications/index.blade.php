@extends('layouts.app')
@section('title', 'Loan Applications')
@section('page-title', 'Loan Applications')
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Applications</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>Customer</th><th>Type</th><th>Amount</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td>{{ $app->customer?->full_name }}</td><td>{{ $app->loanType?->name }}</td>
                    <td class="text-right">{{ number_format($app->applied_amount, 2) }}</td><td>{{ $app->duration_months }} mo</td>
                    <td><span class="badge badge-{{ $app->approval_status == 'approved' ? 'success' : ($app->approval_status == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($app->approval_status) }}</span></td>
                    <td><a href="{{ route('manager.loan-applications.show', $app) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i> Review</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No applications found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())<div class="card-footer">{{ $applications->links() }}</div>@endif
</div>
@endsection
