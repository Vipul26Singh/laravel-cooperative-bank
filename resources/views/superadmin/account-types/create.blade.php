@extends('layouts.app')

@section('title', 'Create Account Type')
@section('page-title', 'Create Account Type')

@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">New Account Type</h3></div>
    <form action="{{ route('superadmin.account-types.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Minimum Balance</label>
                        <input type="number" step="0.01" name="min_balance" class="form-control @error('min_balance') is-invalid @enderror" value="{{ old('min_balance') }}">
                        @error('min_balance') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Interest Rate (%)</label>
                        <input type="number" step="0.01" name="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" value="{{ old('interest_rate') }}">
                        @error('interest_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description') }}</textarea>
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
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Account Type</button>
            <a href="{{ route('superadmin.account-types.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection
