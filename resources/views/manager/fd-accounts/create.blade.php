@extends('layouts.app')
@section('title', 'Open FD')
@section('page-title', 'Open Fixed Deposit')
@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">New FD</h3></div>
    <form action="{{ route('manager.fd-accounts.store') }}" method="POST">@csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-control" required><option value="">-- Select --</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->full_name }} ({{ $c->customer_number }})</option>@endforeach</select></div></div>
                <div class="col-md-6"><div class="form-group"><label>FD Scheme <span class="text-danger">*</span></label>
                    <select name="fd_setup_id" class="form-control" required><option value="">-- Select --</option>@foreach($fdSetups as $s)<option value="{{ $s->id }}">{{ $s->description }} ({{ $s->interest_rate }}% / {{ $s->duration_days }} days)</option>@endforeach</select></div></div>
            </div>
            <div class="row">
                <div class="col-md-4"><div class="form-group"><label>Principal Amount <span class="text-danger">*</span></label><input type="number" step="0.01" name="principal_amount" class="form-control" required></div></div>
                <div class="col-md-4"><div class="form-group"><label>FD Date <span class="text-danger">*</span></label><input type="date" name="fd_date" class="form-control" value="{{ date('Y-m-d') }}" required></div></div>
                <div class="col-md-4"><div class="form-group"><label>Transaction Mode <span class="text-danger">*</span></label>
                    <select name="transaction_mode" class="form-control" required><option value="cash">Cash</option><option value="cheque">Cheque</option></select></div></div>
            </div>
        </div>
        <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-save"></i> Open FD</button> <a href="{{ route('manager.fd-accounts.index') }}" class="btn btn-default">Cancel</a></div>
    </form>
</div>
@endsection
