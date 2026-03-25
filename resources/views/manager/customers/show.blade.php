@extends('layouts.app')
@section('title', 'Customer Details')
@section('page-title', 'Customer Details')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $customer->full_name }} <span class="badge badge-{{ $customer->approval_status == 'approved' ? 'success' : ($customer->approval_status == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($customer->approval_status) }}</span></h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>Customer #</th><td>{{ $customer->customer_number }}</td></tr>
                    <tr><th>Full Name</th><td>{{ $customer->full_name }}</td></tr>
                    <tr><th>Gender</th><td>{{ $customer->gender }}</td></tr>
                    <tr><th>DOB</th><td>{{ $customer->dob?->format('d M Y') ?? '-' }}</td></tr>
                    <tr><th>Mobile</th><td>{{ $customer->mobile }}</td></tr>
                    <tr><th>Email</th><td>{{ $customer->email ?? '-' }}</td></tr>
                    <tr><th>Address</th><td>{{ $customer->residential_address }}</td></tr>
                    <tr><th>Branch</th><td>{{ $customer->branch?->name ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>PAN</th><td>{{ $customer->pan_number ?? '-' }}</td></tr>
                    <tr><th>Aadhaar / UID</th><td>{{ $customer->uid_number ?? '-' }}</td></tr>
                    <tr><th>Nominee</th><td>{{ $customer->nominee_name ?? '-' }}</td></tr>
                    <tr><th>Nominee Relation</th><td>{{ $customer->nominee_relation ?? '-' }}</td></tr>
                    <tr><th>Member Active</th><td>{{ $customer->is_member_active ? 'Yes' : 'No' }}</td></tr>
                    <tr><th>Approval Remark</th><td>{{ $customer->approver_remark ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    @if($customer->approval_status === 'pending')
    <div class="card-footer">
        <form action="{{ route('manager.customers.approve', $customer) }}" method="POST" class="d-inline">@csrf<button class="btn btn-success"><i class="fas fa-check"></i> Approve</button></form>
        <form action="{{ route('manager.customers.reject', $customer) }}" method="POST" class="d-inline ml-2">@csrf
            <input type="text" name="rejection_reason" class="form-control d-inline w-50" placeholder="Rejection reason (optional)">
            <button class="btn btn-danger"><i class="fas fa-times"></i> Reject</button>
        </form>
    </div>
    @endif
</div>
@endsection
