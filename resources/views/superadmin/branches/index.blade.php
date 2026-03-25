@extends('layouts.app')

@section('title', 'Branches')
@section('page-title', 'Manage Branches')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Branches</h3>
        <div class="card-tools">
            <a href="{{ route('superadmin.branches.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Branch</a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Opening Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                <tr>
                    <td>{{ $branch->id }}</td>
                    <td>{{ $branch->name }}</td>
                    <td><code>{{ $branch->code }}</code></td>
                    <td>{{ $branch->opening_date?->format('d M Y') ?? '-' }}</td>
                    <td>
                        @if($branch->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('superadmin.branches.edit', $branch) }}" class="btn btn-xs btn-info"><i class="fas fa-edit"></i> Edit</a>
                        @if($branch->is_active)
                        <form action="{{ route('superadmin.branches.destroy', $branch) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate this branch?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-ban"></i> Deactivate</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No branches found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($branches->hasPages())
    <div class="card-footer clearfix">{{ $branches->links() }}</div>
    @endif
</div>
@endsection
