@extends('layouts.app')
@section('title', 'Installment Schedule')
@section('page-title', 'Installment Schedule — Loan #' . $loan->loan_number)
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $loan->customer?->full_name }} — {{ $loan->loanType?->name }}</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped text-nowrap">
            <thead><tr><th>#</th><th>Due Date</th><th>EMI</th><th>Principal</th><th>Interest</th><th>Balance</th></tr></thead>
            <tbody>
                @foreach($schedule as $row)
                <tr><td>{{ $row['installment_no'] ?? $loop->iteration }}</td><td>{{ \Carbon\Carbon::parse($row['due_date'])->format('d M Y') }}</td><td>{{ number_format($row['emi'] ?? 0, 2) }}</td><td>{{ number_format($row['principal'] ?? 0, 2) }}</td><td>{{ number_format($row['interest'] ?? 0, 2) }}</td><td>{{ number_format($row['balance'] ?? 0, 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
