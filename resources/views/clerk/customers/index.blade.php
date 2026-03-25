@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customer List')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>Registered Customers</h3>
                <div class="card-tools">
                    <a href="{{ route('clerk.customers.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Register New Customer
                    </a>
                </div>
            </div>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Search/Filter Bar --}}
                <form method="GET" action="{{ route('clerk.customers.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Search by name or mobile..."
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        @if(request()->hasAny(['search', 'status']))
                            <div class="col-md-2">
                                <a href="{{ route('clerk.customers.index') }}" class="btn btn-default">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th width="50">No.</th>
                                <th>Customer Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>City</th>
                                <th width="120">Status</th>
                                <th width="130">Registered On</th>
                                <th width="100" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers ?? [] as $index => $customer)
                                <tr>
                                    <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $customer->full_name ?? ($customer->first_name . ' ' . $customer->last_name) }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $customer->customer_code ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $customer->mobile }}</td>
                                    <td>{{ $customer->email ?? '-' }}</td>
                                    <td>{{ $customer->city?->name ?? '-' }}</td>
                                    <td class="text-center">
                                        @if(($customer->status ?? 'Pending') === 'Approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif(($customer->status ?? 'Pending') === 'Rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $customer->created_at?->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('clerk.customers.show', $customer->id) }}"
                                           class="btn btn-info btn-xs" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                        No customers found.
                                        <a href="{{ route('clerk.customers.create') }}">Register the first customer</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($customers) && $customers->hasPages())
                    <div class="mt-3">
                        {{ $customers->appends(request()->query())->links() }}
                    </div>
                @endif

            </div>
            <div class="card-footer text-muted">
                @if(isset($customers))
                    Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }}
                    of {{ $customers->total() }} customers
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
