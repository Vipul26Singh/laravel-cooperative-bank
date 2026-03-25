@extends('layouts.app')

@section('title', 'Edit Loan Type')
@section('page-title', 'Edit Loan Type')

@section('content')
<div class="card card-warning">
    <div class="card-header"><h3 class="card-title">Edit: {{ $loanType->name }}</h3></div>
    <form action="{{ route('superadmin.loan-types.update', $loanType) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $loanType->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Interest Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" value="{{ old('interest_rate', $loanType->interest_rate) }}" required>
                        @error('interest_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Min Amount</label>
                        <input type="number" step="0.01" name="min_amount" class="form-control @error('min_amount') is-invalid @enderror" value="{{ old('min_amount', $loanType->min_amount) }}">
                        @error('min_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Max Amount</label>
                        <input type="number" step="0.01" name="max_amount" class="form-control @error('max_amount') is-invalid @enderror" value="{{ old('max_amount', $loanType->max_amount) }}">
                        @error('max_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Max Duration (months)</label>
                        <input type="number" name="max_duration" class="form-control @error('max_duration') is-invalid @enderror" value="{{ old('max_duration', $loanType->max_duration) }}">
                        @error('max_duration') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $loanType->description) }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active" {{ old('is_active', $loanType->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Loan Type</button>
            <a href="{{ route('superadmin.loan-types.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection
