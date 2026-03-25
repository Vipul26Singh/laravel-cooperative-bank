@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Manage Users')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Users</h3>
        <div class="card-tools">
            <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New User</a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge badge-primary">{{ $user->role?->name ?? '-' }}</span></td>
                    <td>{{ $user->branch?->name ?? '-' }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-xs btn-info"><i class="fas fa-edit"></i> Edit</a>
                        @if($user->is_active && $user->id !== auth()->id())
                        <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate this user?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-ban"></i> Deactivate</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer clearfix">{{ $users->links() }}</div>
    @endif
</div>
@endsection
