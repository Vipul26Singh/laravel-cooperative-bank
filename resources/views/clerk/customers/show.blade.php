@extends('layouts.app')
@section('title', 'Customer Details')
@section('page-title', 'Customer Details')
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $customer->full_name }} <span class="badge badge-{{ $customer->approval_status == 'approved' ? 'success' : ($customer->approval_status == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($customer->approval_status) }}</span></h3></div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Customer #</th><td>{{ $customer->customer_number }}</td></tr>
            <tr><th>Full Name</th><td>{{ $customer->full_name }}</td></tr>
            <tr><th>Gender</th><td>{{ $customer->gender }}</td></tr>
            <tr><th>DOB</th><td>{{ $customer->dob?->format('d M Y') ?? '-' }}</td></tr>
            <tr><th>Mobile</th><td>{{ $customer->mobile }}</td></tr>
            <tr><th>Email</th><td>{{ $customer->email ?? '-' }}</td></tr>
            <tr><th>Address</th><td>{{ $customer->residential_address }}</td></tr>
            <tr><th>PAN</th><td>{{ $customer->pan_number ?? '-' }}</td></tr>
            <tr><th>Aadhaar / UID</th><td>{{ $customer->uid_number ?? '-' }}</td></tr>
            <tr><th>Branch</th><td>{{ $customer->branch?->name ?? '-' }}</td></tr>
            <tr><th>Status</th><td>{{ ucfirst($customer->approval_status) }}</td></tr>
        </table>
    </div>
</div>
@endsection
