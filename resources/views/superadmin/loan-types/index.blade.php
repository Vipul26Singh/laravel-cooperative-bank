@extends('layouts.app')

@section('title', 'Loan Types')
@section('page-title', 'Manage Loan Types')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Loan Types</h3>
        <div class="card-tools">
            <a href="{{ route('superadmin.loan-types.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Loan Type</a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Interest Rate</th>
                    <th>Duration</th>
                    <th>Max Amount</th>
                    <th>Installments</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loanTypes as $lt)
                <tr>
                    <td>{{ $lt->id }}</td>
                    <td>{{ $lt->name }}</td>
                    <td>{{ $lt->interest_rate }}%</td>
                    <td>{{ $lt->duration_months }} months</td>
                    <td>{{ number_format($lt->max_amount, 2) }}</td>
                    <td>{{ $lt->num_installments }}</td>
                    <td>
                        @if($lt->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('superadmin.loan-types.edit', $lt) }}" class="btn btn-xs btn-info"><i class="fas fa-edit"></i> Edit</a>
                        @if($lt->is_active)
                        <form action="{{ route('superadmin.loan-types.destroy', $lt) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate this loan type?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-ban"></i> Deactivate</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">No loan types found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($loanTypes->hasPages())
    <div class="card-footer clearfix">{{ $loanTypes->links() }}</div>
    @endif
</div>
@endsection
