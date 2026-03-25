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
                    <th>Description</th>
                    <th>Duration (days)</th>
                    <th>Interest Rate</th>
                    <th>Senior Citizen</th>
                    <th>Special ROI</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fdSetups as $fd)
                <tr>
                    <td>{{ $fd->id }}</td>
                    <td>{{ Str::limit($fd->description, 40) }}</td>
                    <td>{{ $fd->duration_days }}</td>
                    <td>{{ $fd->interest_rate }}%</td>
                    <td>{!! $fd->is_senior_citizen ? '<span class="badge badge-info">Yes</span>' : 'No' !!}</td>
                    <td>{!! $fd->is_special_roi ? '<span class="badge badge-warning">Yes</span>' : 'No' !!}</td>
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
