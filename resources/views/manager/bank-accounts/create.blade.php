@extends('layouts.app')
@section('title', 'Open Account')
@section('page-title', 'Open Bank Account')
@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">New Account</h3></div>
    <form action="{{ route('manager.bank-accounts.store') }}" method="POST">@csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)<option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->customer_number }})</option>@endforeach
                        </select>@error('customer_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Account Type <span class="text-danger">*</span></label>
                        <select name="account_type_id" class="form-control @error('account_type_id') is-invalid @enderror" required>
                            <option value="">-- Select Type --</option>
                            @foreach($accountTypes as $t)<option value="{{ $t->id }}" {{ old('account_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach
                        </select>@error('account_type_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Opening Date <span class="text-danger">*</span></label><input type="date" name="opening_date" class="form-control" value="{{ old('opening_date', date('Y-m-d')) }}" required></div></div>
                <div class="col-md-6"><div class="form-group"><label>Opening Balance</label><input type="number" step="0.01" name="balance" class="form-control" value="{{ old('balance', 0) }}"></div></div>
            </div>
        </div>
        <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-save"></i> Open Account</button> <a href="{{ route('manager.bank-accounts.index') }}" class="btn btn-default">Cancel</a></div>
    </form>
</div>
@endsection
