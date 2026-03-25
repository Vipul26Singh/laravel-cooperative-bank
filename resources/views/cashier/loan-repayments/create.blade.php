@extends('layouts.app')
@section('title', 'Record Repayment')
@section('page-title', 'Record Loan Repayment')
@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Loan Repayment</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline mb-3">
            <input type="text" name="loan_number" class="form-control mr-2" placeholder="Enter Loan Number" value="{{ request('loan_number') }}">
            <button class="btn btn-info"><i class="fas fa-search"></i> Search</button>
        </form>
        @if($loan)
        <div class="alert alert-info">
            <strong>{{ $loan->customer?->full_name }}</strong> — {{ $loan->loanType?->name }}<br>
            Outstanding: <strong>{{ number_format($loan->outstanding_balance, 2) }}</strong> | EMI: {{ number_format($loan->installment_amount, 2) }}
        </div>
        <form action="{{ route('cashier.loan-repayments.store') }}" method="POST">@csrf
            <input type="hidden" name="loan_id" value="{{ $loan->id }}">
            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Amount Paid <span class="text-danger">*</span></label><input type="number" step="0.01" name="amount_paid" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Principal Paid <span class="text-danger">*</span></label><input type="number" step="0.01" name="principal_paid" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Interest Paid <span class="text-danger">*</span></label><input type="number" step="0.01" name="interest_paid" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Payment Date <span class="text-danger">*</span></label><input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required></div></div>
            </div>
            <div class="form-group"><label>Mode <span class="text-danger">*</span></label>
                <select name="transaction_mode" class="form-control w-25" required><option value="cash">Cash</option><option value="cheque">Cheque</option></select>
            </div>
            <button class="btn btn-success"><i class="fas fa-save"></i> Record Repayment</button>
        </form>
        @elseif(request('loan_number'))
        <div class="alert alert-warning">No active loan found with that number in your branch.</div>
        @endif
    </div>
</div>
@endsection
