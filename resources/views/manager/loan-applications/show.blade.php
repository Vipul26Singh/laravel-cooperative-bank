@extends('layouts.app')
@section('title', 'Loan Application')
@section('page-title', 'Loan Application Review')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>Customer</th><td>{{ $loanApplication->customer?->full_name }}</td></tr>
                    <tr><th>Loan Type</th><td>{{ $loanApplication->loanType?->name }}</td></tr>
                    <tr><th>Applied Amount</th><td>{{ number_format($loanApplication->applied_amount, 2) }}</td></tr>
                    <tr><th>Duration</th><td>{{ $loanApplication->duration_months }} months</td></tr>
                    <tr><th>Purpose</th><td>{{ $loanApplication->loan_purpose }}</td></tr>
                    <tr><th>Frequency</th><td>{{ $loanApplication->frequency }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>Guarantor 1</th><td>{{ $loanApplication->guarantor1?->full_name ?? '-' }}</td></tr>
                    <tr><th>Guarantor 2</th><td>{{ $loanApplication->guarantor2?->full_name ?? '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $loanApplication->approval_status == 'approved' ? 'success' : ($loanApplication->approval_status == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($loanApplication->approval_status) }}</span></td></tr>
                    <tr><th>Approved Amount</th><td>{{ $loanApplication->approved_amount ? number_format($loanApplication->approved_amount, 2) : '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    @if($loanApplication->approval_status === 'pending')
    <div class="card-footer">
        <form action="{{ route('manager.loan-applications.approve', $loanApplication) }}" method="POST" class="d-inline">@csrf
            <input type="number" step="0.01" name="approved_amount" class="form-control d-inline w-25" placeholder="Sanctioned amount">
            <button class="btn btn-success"><i class="fas fa-check"></i> Approve</button>
        </form>
        <form action="{{ route('manager.loan-applications.reject', $loanApplication) }}" method="POST" class="d-inline ml-2">@csrf
            <input type="text" name="rejection_reason" class="form-control d-inline w-25" placeholder="Reason">
            <button class="btn btn-danger"><i class="fas fa-times"></i> Reject</button>
        </form>
    </div>
    @endif
</div>
@endsection
