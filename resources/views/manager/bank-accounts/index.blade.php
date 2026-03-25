@extends('layouts.app')
@section('title', 'Bank Accounts')
@section('page-title', 'Bank Accounts')
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Branch Accounts</h3>
        <div class="card-tools"><a href="{{ route('manager.bank-accounts.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Open Account</a></div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>Account #</th><th>Customer</th><th>Type</th><th>Balance</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($accounts as $a)
                <tr>
                    <td>{{ $a->account_number }}</td>
                    <td>{{ $a->customer?->full_name }}</td>
                    <td>{{ $a->accountType?->name }}</td>
                    <td class="text-right">{{ number_format($a->balance, 2) }}</td>
                    <td><span class="badge badge-{{ $a->is_active ? 'success' : 'danger' }}">{{ $a->is_active ? 'Active' : 'Closed' }}</span></td>
                    <td><a href="{{ route('manager.bank-accounts.show', $a) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No accounts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($accounts->hasPages())<div class="card-footer">{{ $accounts->links() }}</div>@endif
</div>
@endsection
