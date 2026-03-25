@extends('layouts.app')

@section('title', 'Edit Branch')
@section('page-title', 'Edit Branch')

@section('content')
<div class="card card-warning">
    <div class="card-header"><h3 class="card-title">Edit: {{ $branch->name }}</h3></div>
    <form action="{{ route('superadmin.branches.update', $branch) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Branch Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $branch->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Branch Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $branch->code) }}" required>
                        @error('code') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Opening Date</label>
                        <input type="date" name="opening_date" class="form-control @error('opening_date') is-invalid @enderror" value="{{ old('opening_date', $branch->opening_date?->format('Y-m-d')) }}">
                        @error('opening_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $branch->address) }}</textarea>
                @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active" {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Branch</button>
            <a href="{{ route('superadmin.branches.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection
