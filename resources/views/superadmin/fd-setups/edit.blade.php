@extends('layouts.app')

@section('title', 'Edit FD Setup')
@section('page-title', 'Edit FD Setup')

@section('content')
<div class="card card-warning">
    <div class="card-header"><h3 class="card-title">Edit: {{ $fdSetup->name }}</h3></div>
    <form action="{{ route('superadmin.fd-setups.update', $fdSetup) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Scheme Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $fdSetup->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Duration (months) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_months" class="form-control @error('duration_months') is-invalid @enderror" value="{{ old('duration_months', $fdSetup->duration_months) }}" required>
                        @error('duration_months') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Interest Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" value="{{ old('interest_rate', $fdSetup->interest_rate) }}" required>
                        @error('interest_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Min Amount</label>
                        <input type="number" step="0.01" name="min_amount" class="form-control @error('min_amount') is-invalid @enderror" value="{{ old('min_amount', $fdSetup->min_amount) }}">
                        @error('min_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Max Amount</label>
                        <input type="number" step="0.01" name="max_amount" class="form-control @error('max_amount') is-invalid @enderror" value="{{ old('max_amount', $fdSetup->max_amount) }}">
                        @error('max_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $fdSetup->description) }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active" {{ old('is_active', $fdSetup->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update FD Setup</button>
            <a href="{{ route('superadmin.fd-setups.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection
