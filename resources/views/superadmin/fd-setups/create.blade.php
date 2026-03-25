@extends('layouts.app')

@section('title', 'Create FD Setup')
@section('page-title', 'Create FD Setup')

@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">New FD Scheme</h3></div>
    <form action="{{ route('superadmin.fd-setups.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Duration (days) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror" value="{{ old('duration_days') }}" required>
                        @error('duration_days') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Interest Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" value="{{ old('interest_rate') }}" required>
                        @error('interest_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Options</label>
                        <div class="mt-2">
                            <div class="custom-control custom-checkbox d-inline mr-3">
                                <input type="hidden" name="is_senior_citizen" value="0">
                                <input type="checkbox" name="is_senior_citizen" value="1" class="custom-control-input" id="is_senior_citizen" {{ old('is_senior_citizen') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_senior_citizen">Senior Citizen</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline">
                                <input type="hidden" name="is_special_roi" value="0">
                                <input type="checkbox" name="is_special_roi" value="1" class="custom-control-input" id="is_special_roi" {{ old('is_special_roi') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_special_roi">Special ROI</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" required>{{ old('description') }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save FD Setup</button>
            <a href="{{ route('superadmin.fd-setups.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection
