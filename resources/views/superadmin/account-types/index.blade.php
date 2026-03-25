@extends('layouts.app')

@section('title', 'Account Types')
@section('page-title', 'Manage Account Types')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Account Types</h3>
        <div class="card-tools">
            <a href="{{ route('superadmin.account-types.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Account Type</a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Min Balance</th>
                    <th>Interest Rate</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accountTypes as $at)
                <tr>
                    <td>{{ $at->id }}</td>
                    <td>{{ $at->name }}</td>
                    <td>{{ $at->type }}</td>
                    <td>{{ number_format($at->minimum_balance, 2) }}</td>
                    <td>{{ $at->interest_rate }}%</td>
                    <td>
                        @if($at->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('superadmin.account-types.edit', $at) }}" class="btn btn-xs btn-info"><i class="fas fa-edit"></i> Edit</a>
                        @if($at->is_active)
                        <form action="{{ route('superadmin.account-types.destroy', $at) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate this account type?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-ban"></i> Deactivate</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No account types found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($accountTypes->hasPages())
    <div class="card-footer clearfix">{{ $accountTypes->links() }}</div>
    @endif
</div>
@endsection
