@extends('layouts.app')
@section('title', 'Loan Application')
@section('page-title', 'Loan Application Details')
@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Customer</th><td>{{ $loanApplication->customer?->full_name }}</td></tr>
            <tr><th>Loan Type</th><td>{{ $loanApplication->loanType?->name }}</td></tr>
            <tr><th>Applied Amount</th><td>{{ number_format($loanApplication->applied_amount, 2) }}</td></tr>
            <tr><th>Duration</th><td>{{ $loanApplication->duration_months }} months</td></tr>
            <tr><th>Purpose</th><td>{{ $loanApplication->loan_purpose }}</td></tr>
            <tr><th>Frequency</th><td>{{ $loanApplication->frequency }}</td></tr>
            <tr><th>Guarantor 1</th><td>{{ $loanApplication->guarantor1?->full_name ?? '-' }}</td></tr>
            <tr><th>Guarantor 2</th><td>{{ $loanApplication->guarantor2?->full_name ?? '-' }}</td></tr>
            <tr><th>Status</th><td><span class="badge badge-{{ $loanApplication->approval_status == 'approved' ? 'success' : ($loanApplication->approval_status == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($loanApplication->approval_status) }}</span></td></tr>
            <tr><th>Approved Amount</th><td>{{ $loanApplication->approved_amount ? number_format($loanApplication->approved_amount, 2) : '-' }}</td></tr>
            <tr><th>Remark</th><td>{{ $loanApplication->approver_remark ?? '-' }}</td></tr>
        </table>
    </div>
</div>
@endsection
