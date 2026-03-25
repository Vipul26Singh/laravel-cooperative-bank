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
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name ?? '') }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $company->email ?? '') }}">
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $company->phone ?? '') }}">
                        @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>GST No.</label>
                        <input type="text" name="gst_no" class="form-control @error('gst_no') is-invalid @enderror" value="{{ old('gst_no', $company->gst_no ?? '') }}">
                        @error('gst_no') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>PAN No.</label>
                        <input type="text" name="pan_no" class="form-control @error('pan_no') is-invalid @enderror" value="{{ old('pan_no', $company->pan_no ?? '') }}">
                        @error('pan_no') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
