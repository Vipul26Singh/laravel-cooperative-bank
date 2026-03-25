@extends('layouts.app')

@section('title', 'Edit Account Type')
@section('page-title', 'Edit Account Type')

@section('content')
<div class="card card-warning">
    <div class="card-header"><h3 class="card-title">Edit: {{ $accountType->name }}</h3></div>
    <form action="{{ route('superadmin.account-types.update', $accountType) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $accountType->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">-- Select --</option>
                            <option value="Savings" {{ old('type', $accountType->type) == 'Savings' ? 'selected' : '' }}>Savings</option>
                            <option value="Current" {{ old('type', $accountType->type) == 'Current' ? 'selected' : '' }}>Current</option>
                            <option value="OD" {{ old('type', $accountType->type) == 'OD' ? 'selected' : '' }}>Overdraft (OD)</option>
                        </select>
                        @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Minimum Balance</label>
                        <input type="number" step="0.01" name="minimum_balance" class="form-control @error('minimum_balance') is-invalid @enderror" value="{{ old('minimum_balance', $accountType->minimum_balance) }}">
                        @error('minimum_balance') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Interest Rate (%)</label>
                        <input type="number" step="0.01" name="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" value="{{ old('interest_rate', $accountType->interest_rate) }}">
                        @error('interest_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active" {{ old('is_active', $accountType->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Account Type</button>
            <a href="{{ route('superadmin.account-types.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection
