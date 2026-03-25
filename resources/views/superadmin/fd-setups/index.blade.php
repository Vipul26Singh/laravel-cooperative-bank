@extends('layouts.app')

@section('title', 'FD Setups')
@section('page-title', 'Manage FD Setups')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All FD Schemes</h3>
        <div class="card-tools">
            <a href="{{ route('superadmin.fd-setups.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New FD Setup</a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Duration (months)</th>
                    <th>Interest Rate</th>
                    <th>Min Amount</th>
                    <th>Max Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fdSetups as $fd)
                <tr>
                    <td>{{ $fd->id }}</td>
                    <td>{{ $fd->name }}</td>
                    <td>{{ $fd->duration_months }}</td>
                    <td>{{ $fd->interest_rate }}%</td>
                    <td>{{ $fd->min_amount ? number_format($fd->min_amount, 2) : '-' }}</td>
                    <td>{{ $fd->max_amount ? number_format($fd->max_amount, 2) : '-' }}</td>
                    <td>
                        @if($fd->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('superadmin.fd-setups.edit', $fd) }}" class="btn btn-xs btn-info"><i class="fas fa-edit"></i> Edit</a>
                        @if($fd->is_active)
                        <form action="{{ route('superadmin.fd-setups.destroy', $fd) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate this FD setup?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-ban"></i> Deactivate</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">No FD setups found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($fdSetups->hasPages())
    <div class="card-footer clearfix">{{ $fdSetups->links() }}</div>
    @endif
</div>
@endsection
