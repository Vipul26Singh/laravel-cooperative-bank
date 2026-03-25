@extends('layouts.app')

@section('title', 'Register New Customer')
@section('page-title', 'Register New Customer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Customer Registration Form</h3>
                <div class="card-tools">
                    <a href="{{ route('clerk.customers.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <form action="{{ route('clerk.customers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h6><i class="fas fa-ban"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Personal Information --}}
                    <div class="card card-secondary card-outline mb-3">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-id-card mr-2"></i>Personal Information</h5></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="first_name"
                                               class="form-control @error('first_name') is-invalid @enderror"
                                               value="{{ old('first_name') }}" placeholder="Enter first name" required>
                                        @error('first_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="middle_name">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name"
                                               class="form-control @error('middle_name') is-invalid @enderror"
                                               value="{{ old('middle_name') }}" placeholder="Enter middle name">
                                        @error('middle_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" id="last_name"
                                               class="form-control @error('last_name') is-invalid @enderror"
                                               value="{{ old('last_name') }}" placeholder="Enter last name" required>
                                        @error('last_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="dob">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="dob" id="dob"
                                               class="form-control @error('dob') is-invalid @enderror"
                                               value="{{ old('dob') }}" required>
                                        @error('dob')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gender">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" id="gender"
                                                class="form-control @error('gender') is-invalid @enderror" required>
                                            <option value="">-- Select Gender --</option>
                                            <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="marital_status">Marital Status</label>
                                        <select name="marital_status" id="marital_status"
                                                class="form-control @error('marital_status') is-invalid @enderror">
                                            <option value="">-- Select Status --</option>
                                            <option value="Single" {{ old('marital_status') === 'Single' ? 'selected' : '' }}>Single</option>
                                            <option value="Married" {{ old('marital_status') === 'Married' ? 'selected' : '' }}>Married</option>
                                            <option value="Widowed" {{ old('marital_status') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                            <option value="Divorced" {{ old('marital_status') === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                        </select>
                                        @error('marital_status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="occupation">Occupation</label>
                                        <input type="text" name="occupation" id="occupation"
                                               class="form-control @error('occupation') is-invalid @enderror"
                                               value="{{ old('occupation') }}" placeholder="Enter occupation">
                                        @error('occupation')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="photo">Photo</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="photo" id="photo"
                                                       class="custom-file-input @error('photo') is-invalid @enderror"
                                                       accept="image/jpeg,image/png,image/jpg">
                                                <label class="custom-file-label" for="photo">Choose photo</label>
                                            </div>
                                        </div>
                                        <small class="text-muted">Accepted: JPG, PNG. Max 2MB.</small>
                                        @error('photo')<span class="text-danger small">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Information --}}
                    <div class="card card-secondary card-outline mb-3">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-phone mr-2"></i>Contact Information</h5></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mobile">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="text" name="mobile" id="mobile"
                                               class="form-control @error('mobile') is-invalid @enderror"
                                               value="{{ old('mobile') }}" placeholder="10-digit mobile number"
                                               maxlength="10" required>
                                        @error('mobile')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="alternate_mobile">Alternate Mobile</label>
                                        <input type="text" name="alternate_mobile" id="alternate_mobile"
                                               class="form-control @error('alternate_mobile') is-invalid @enderror"
                                               value="{{ old('alternate_mobile') }}" placeholder="Alternate mobile"
                                               maxlength="10">
                                        @error('alternate_mobile')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" name="email" id="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}" placeholder="Enter email address">
                                        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Address Information --}}
                    <div class="card card-secondary card-outline mb-3">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Address Information</h5></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address <span class="text-danger">*</span></label>
                                        <textarea name="address" id="address" rows="2"
                                                  class="form-control @error('address') is-invalid @enderror"
                                                  placeholder="Enter full address" required>{{ old('address') }}</textarea>
                                        @error('address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="country_id">Country <span class="text-danger">*</span></label>
                                        <select name="country_id" id="country_id"
                                                class="form-control @error('country_id') is-invalid @enderror" required>
                                            <option value="">-- Select Country --</option>
                                            @foreach($countries ?? [] as $country)
                                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="state_id">State <span class="text-danger">*</span></label>
                                        <select name="state_id" id="state_id"
                                                class="form-control @error('state_id') is-invalid @enderror" required>
                                            <option value="">-- Select State --</option>
                                            @foreach($states ?? [] as $state)
                                                <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('state_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city_id">City <span class="text-danger">*</span></label>
                                        <select name="city_id" id="city_id"
                                                class="form-control @error('city_id') is-invalid @enderror" required>
                                            <option value="">-- Select City --</option>
                                            @foreach($cities ?? [] as $city)
                                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                    {{ $city->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('city_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pincode">Pincode <span class="text-danger">*</span></label>
                                        <input type="text" name="pincode" id="pincode"
                                               class="form-control @error('pincode') is-invalid @enderror"
                                               value="{{ old('pincode') }}" placeholder="6-digit pincode"
                                               maxlength="6" required>
                                        @error('pincode')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Identity Documents --}}
                    <div class="card card-secondary card-outline mb-3">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-id-badge mr-2"></i>Identity Documents (KYC)</h5></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pan_number">PAN Number</label>
                                        <input type="text" name="pan_number" id="pan_number"
                                               class="form-control @error('pan_number') is-invalid @enderror"
                                               value="{{ old('pan_number') }}" placeholder="ABCDE1234F"
                                               maxlength="10" style="text-transform: uppercase;">
                                        @error('pan_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pan_document">PAN Document</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="pan_document" id="pan_document"
                                                       class="custom-file-input @error('pan_document') is-invalid @enderror"
                                                       accept="image/jpeg,image/png,application/pdf">
                                                <label class="custom-file-label" for="pan_document">Choose file</label>
                                            </div>
                                        </div>
                                        <small class="text-muted">JPG, PNG or PDF. Max 2MB.</small>
                                        @error('pan_document')<span class="text-danger small">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="uid_number">Aadhaar / UID Number</label>
                                        <input type="text" name="uid_number" id="uid_number"
                                               class="form-control @error('uid_number') is-invalid @enderror"
                                               value="{{ old('uid_number') }}" placeholder="12-digit Aadhaar number"
                                               maxlength="12">
                                        @error('uid_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="uid_document">Aadhaar Document</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="uid_document" id="uid_document"
                                                       class="custom-file-input @error('uid_document') is-invalid @enderror"
                                                       accept="image/jpeg,image/png,application/pdf">
                                                <label class="custom-file-label" for="uid_document">Choose file</label>
                                            </div>
                                        </div>
                                        <small class="text-muted">JPG, PNG or PDF. Max 2MB.</small>
                                        @error('uid_document')<span class="text-danger small">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nominee Information --}}
                    <div class="card card-secondary card-outline mb-3">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-user-friends mr-2"></i>Nominee Information</h5></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nominee_name">Nominee Name</label>
                                        <input type="text" name="nominee_name" id="nominee_name"
                                               class="form-control @error('nominee_name') is-invalid @enderror"
                                               value="{{ old('nominee_name') }}" placeholder="Enter nominee name">
                                        @error('nominee_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nominee_relation">Relation with Nominee</label>
                                        <select name="nominee_relation" id="nominee_relation"
                                                class="form-control @error('nominee_relation') is-invalid @enderror">
                                            <option value="">-- Select Relation --</option>
                                            <option value="Spouse" {{ old('nominee_relation') === 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                            <option value="Father" {{ old('nominee_relation') === 'Father' ? 'selected' : '' }}>Father</option>
                                            <option value="Mother" {{ old('nominee_relation') === 'Mother' ? 'selected' : '' }}>Mother</option>
                                            <option value="Son" {{ old('nominee_relation') === 'Son' ? 'selected' : '' }}>Son</option>
                                            <option value="Daughter" {{ old('nominee_relation') === 'Daughter' ? 'selected' : '' }}>Daughter</option>
                                            <option value="Brother" {{ old('nominee_relation') === 'Brother' ? 'selected' : '' }}>Brother</option>
                                            <option value="Sister" {{ old('nominee_relation') === 'Sister' ? 'selected' : '' }}>Sister</option>
                                            <option value="Other" {{ old('nominee_relation') === 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('nominee_relation')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nominee_dob">Nominee Date of Birth</label>
                                        <input type="date" name="nominee_dob" id="nominee_dob"
                                               class="form-control @error('nominee_dob') is-invalid @enderror"
                                               value="{{ old('nominee_dob') }}">
                                        @error('nominee_dob')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nominee_mobile">Nominee Mobile</label>
                                        <input type="text" name="nominee_mobile" id="nominee_mobile"
                                               class="form-control @error('nominee_mobile') is-invalid @enderror"
                                               value="{{ old('nominee_mobile') }}" placeholder="Nominee mobile number"
                                               maxlength="10">
                                        @error('nominee_mobile')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nominee_address">Nominee Address</label>
                                        <input type="text" name="nominee_address" id="nominee_address"
                                               class="form-control @error('nominee_address') is-invalid @enderror"
                                               value="{{ old('nominee_address') }}" placeholder="Nominee address">
                                        @error('nominee_address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Register Customer
                    </button>
                    <a href="{{ route('clerk.customers.index') }}" class="btn btn-default ml-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show filename on custom file input
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            var fileName = this.files[0] ? this.files[0].name : 'Choose file';
            this.nextElementSibling.innerText = fileName;
        });
    });
</script>
@endpush
