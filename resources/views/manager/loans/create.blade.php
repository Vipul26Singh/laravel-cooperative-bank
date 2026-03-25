@extends('layouts.app')
@section('title', 'Disburse Loan')
@section('page-title', 'Disburse Loan')
@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Loan Disbursement</h3></div>
    <form action="{{ route('manager.loans.store') }}" method="POST">@csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Approved Application</label>
                    <select name="loan_application_id" class="form-control"><option value="">-- Select --</option>@foreach($loanApplications as $app)<option value="{{ $app->id }}">{{ $app->customer?->full_name }} - {{ number_format($app->approved_amount ?? $app->applied_amount, 2) }}</option>@endforeach</select></div></div>
                <div class="col-md-6"><div class="form-group"><label>Loan Type <span class="text-danger">*</span></label>
                    <select name="loan_type_id" class="form-control" required><option value="">-- Select --</option>@foreach($loanTypes as $t)<option value="{{ $t->id }}">{{ $t->name }} ({{ $t->interest_rate }}%)</option>@endforeach</select></div></div>
            </div>
            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Customer <span class="text-danger">*</span></label><input type="number" name="customer_id" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Amount <span class="text-danger">*</span></label><input type="number" step="0.01" name="amount" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Interest Rate (%) <span class="text-danger">*</span></label><input type="number" step="0.01" name="interest_rate" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Duration (months) <span class="text-danger">*</span></label><input type="number" name="duration_months" class="form-control" required></div></div>
            </div>
            <div class="row">
                <div class="col-md-4"><div class="form-group"><label>First Installment Date <span class="text-danger">*</span></label><input type="date" name="first_installment_date" class="form-control" required></div></div>
                <div class="col-md-4"><div class="form-group"><label>Frequency <span class="text-danger">*</span></label>
                    <select name="frequency" class="form-control" required><option value="MONTHLY">Monthly</option><option value="WEEKLY">Weekly</option><option value="DAILY">Daily</option></select></div></div>
            </div>
        </div>
        <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-save"></i> Disburse</button> <a href="{{ route('manager.loans.index') }}" class="btn btn-default">Cancel</a></div>
    </form>
</div>
@endsection
