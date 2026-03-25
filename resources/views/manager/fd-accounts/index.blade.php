@extends('layouts.app')
@section('title', 'FD Accounts')
@section('page-title', 'FD Accounts')
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Fixed Deposits</h3>
        <div class="card-tools"><a href="{{ route('manager.fd-accounts.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Open FD</a></div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>FD #</th><th>Customer</th><th>Scheme</th><th>Principal</th><th>Rate</th><th>Maturity</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($fdAccounts as $fd)
                <tr>
                    <td>{{ $fd->fd_number }}</td><td>{{ $fd->customer?->full_name }}</td><td>{{ $fd->fdSetup?->description }}</td>
                    <td class="text-right">{{ number_format($fd->principal_amount, 2) }}</td><td>{{ $fd->interest_rate }}%</td>
                    <td>{{ $fd->maturity_date?->format('d M Y') }}</td>
                    <td><span class="badge badge-{{ $fd->is_matured ? 'warning' : ($fd->is_withdrawn ? 'secondary' : 'success') }}">{{ $fd->is_withdrawn ? 'Withdrawn' : ($fd->is_matured ? 'Matured' : 'Active') }}</span></td>
                    <td><a href="{{ route('manager.fd-accounts.show', $fd) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">No FD accounts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($fdAccounts->hasPages())<div class="card-footer">{{ $fdAccounts->links() }}</div>@endif
</div>
@endsection
