@extends('layouts.app')
@section('title', 'FD Details')
@section('page-title', 'FD #' . $fdAccount->fd_number)
@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>FD #</th><td>{{ $fdAccount->fd_number }}</td></tr>
            <tr><th>Customer</th><td>{{ $fdAccount->customer?->full_name }}</td></tr>
            <tr><th>Scheme</th><td>{{ $fdAccount->fdSetup?->description }}</td></tr>
            <tr><th>Principal</th><td>{{ number_format($fdAccount->principal_amount, 2) }}</td></tr>
            <tr><th>Interest Rate</th><td>{{ $fdAccount->interest_rate }}%</td></tr>
            <tr><th>FD Date</th><td>{{ $fdAccount->fd_date?->format('d M Y') }}</td></tr>
            <tr><th>Maturity Date</th><td>{{ $fdAccount->maturity_date?->format('d M Y') }}</td></tr>
            <tr><th>Maturity Amount</th><td>{{ number_format($fdAccount->maturity_amount, 2) }}</td></tr>
            <tr><th>Status</th><td><span class="badge badge-{{ $fdAccount->is_matured ? 'warning' : 'success' }}">{{ $fdAccount->is_matured ? 'Matured' : 'Active' }}</span></td></tr>
        </table>
    </div>
</div>
@endsection
