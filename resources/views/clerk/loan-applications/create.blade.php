@extends('layouts.app')
@section('title', 'New Loan Application')
@section('page-title', 'Submit Loan Application')
@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">New Application</h3></div>
    <form action="{{ route('clerk.loan-applications.store') }}" method="POST">@csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required><option value="">-- Select --</option>@foreach($customers as $c)<option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->customer_number }})</option>@endforeach</select>
                    @error('customer_id')<span class="invalid-feedback">{{ $message }}</span>@enderror</div></div>
                <div class="col-md-6"><div class="form-group"><label>Loan Type <span class="text-danger">*</span></label>
                    <select name="loan_type_id" class="form-control @error('loan_type_id') is-invalid @enderror" required><option value="">-- Select --</option>@foreach($loanTypes as $t)<option value="{{ $t->id }}" {{ old('loan_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->interest_rate }}%)</option>@endforeach</select>
                    @error('loan_type_id')<span class="invalid-feedback">{{ $message }}</span>@enderror</div></div>
            </div>
            <div class="row">
                <div class="col-md-4"><div class="form-group"><label>Amount <span class="text-danger">*</span></label><input type="number" step="0.01" name="applied_amount" class="form-control @error('applied_amount') is-invalid @enderror" value="{{ old('applied_amount') }}" required>@error('applied_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror</div></div>
                <div class="col-md-4"><div class="form-group"><label>Duration (months) <span class="text-danger">*</span></label><input type="number" name="duration_months" class="form-control @error('duration_months') is-invalid @enderror" value="{{ old('duration_months') }}" required>@error('duration_months')<span class="invalid-feedback">{{ $message }}</span>@enderror</div></div>
                <div class="col-md-4"><div class="form-group"><label>Frequency <span class="text-danger">*</span></label>
                    <select name="frequency" class="form-control @error('frequency') is-invalid @enderror" required><option value="MONTHLY">Monthly</option><option value="WEEKLY">Weekly</option><option value="DAILY">Daily</option></select>
                    @error('frequency')<span class="invalid-feedback">{{ $message }}</span>@enderror</div></div>
            </div>
            <div class="form-group"><label>Purpose <span class="text-danger">*</span></label><textarea name="loan_purpose" class="form-control @error('loan_purpose') is-invalid @enderror" rows="2" required>{{ old('loan_purpose') }}</textarea>@error('loan_purpose')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Guarantor 1</label>
                    <select name="guarantor1_id" class="form-control"><option value="">-- None --</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->full_name }}</option>@endforeach</select></div></div>
                <div class="col-md-6"><div class="form-group"><label>Guarantor 2</label>
                    <select name="guarantor2_id" class="form-control"><option value="">-- None --</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->full_name }}</option>@endforeach</select></div></div>
            </div>
        </div>
        <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-save"></i> Submit Application</button> <a href="{{ route('clerk.loan-applications.index') }}" class="btn btn-default">Cancel</a></div>
    </form>
</div>
@endsection
