@extends('layouts.app')
@section('title', 'Customers')
@section('page-title', 'Customers')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Branch Customers</h3>
        <div class="card-tools">
            <form class="form-inline" method="GET">
                <select name="approval_status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr><th>#</th><th>Name</th><th>Mobile</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                <tr>
                    <td>{{ $c->customer_number }}</td>
                    <td>{{ $c->full_name }}</td>
                    <td>{{ $c->mobile }}</td>
                    <td>
                        <span class="badge badge-{{ $c->approval_status == 'approved' ? 'success' : ($c->approval_status == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($c->approval_status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('manager.customers.show', $c) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i> View</a>
                        @if($c->approval_status === 'pending')
                        <form action="{{ route('manager.customers.approve', $c) }}" method="POST" class="d-inline"><@csrf><button class="btn btn-xs btn-success"><i class="fas fa-check"></i> Approve</button></form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())<div class="card-footer">{{ $customers->links() }}</div>@endif
</div>
@endsection
