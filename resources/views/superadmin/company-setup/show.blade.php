@extends('layouts.app')

@section('title', 'Company Setup')
@section('page-title', 'Company Setup')

@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Company Configuration</h3></div>
    <form action="{{ route('superadmin.company-setup.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $company->company_name ?? '') }}" required>
                        @error('company_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Registration No.</label>
                        <input type="text" name="registration_no" class="form-control @error('registration_no') is-invalid @enderror" value="{{ old('registration_no', $company->registration_no ?? '') }}">
                        @error('registration_no') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $company->address ?? '') }}</textarea>
                @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $company->city ?? '') }}">
                        @error('city') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $company->state ?? '') }}">
                        @error('state') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $company->country ?? '') }}">
                        @error('country') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode', $company->pincode ?? '') }}">
                        @error('pincode') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $company->phone ?? '') }}">
                        @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $company->email ?? '') }}">
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Established Year</label>
                        <input type="number" name="established_year" class="form-control @error('established_year') is-invalid @enderror" value="{{ old('established_year', $company->established_year ?? '') }}">
                        @error('established_year') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Website</label>
                        <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $company->website ?? '') }}">
                        @error('website') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Logo</label>
                        <div class="custom-file">
                            <input type="file" name="logo" class="custom-file-input @error('logo') is-invalid @enderror" id="logo" accept="image/*">
                            <label class="custom-file-label" for="logo">Choose file</label>
                        </div>
                        @error('logo') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$('.custom-file-input').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
});
</script>
@endpush
