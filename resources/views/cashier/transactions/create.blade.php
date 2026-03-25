@extends('layouts.app')

@section('title', 'New Transaction')
@section('page-title', 'New Bank Transaction')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>Process Transaction</h3>
                <div class="card-tools">
                    <a href="{{ route('cashier.transactions.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <form action="{{ route('cashier.transactions.store') }}" method="POST" id="transactionForm">
                @csrf

                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h6><i class="fas fa-ban"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Account Search --}}
                    <div class="form-group">
                        <label for="account_number">Account Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="account_number" id="account_number"
                                   class="form-control @error('account_number') is-invalid @enderror"
                                   value="{{ old('account_number') }}"
                                   placeholder="Enter account number"
                                   required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" id="searchAccount">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                        @error('account_number')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Account Details (shown after search) --}}
                    <div id="accountDetails" class="card bg-light mb-3" style="display: none;">
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="font-weight-bold">Customer Name</label>
                                        <input type="text" id="customer_name_display" class="form-control form-control-sm" readonly>
                                        <input type="hidden" name="bank_account_id" id="bank_account_id">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="font-weight-bold">Available Balance</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">&#8377;</span>
                                            </div>
                                            <input type="text" id="balance_display" class="form-control form-control-sm" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Account Type</label>
                                        <input type="text" id="account_type_display" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Account Status</label>
                                        <input type="text" id="account_status_display" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Transaction Type --}}
                    <div class="form-group">
                        <label for="transaction_type">Transaction Type <span class="text-danger">*</span></label>
                        <select name="transaction_type" id="transaction_type"
                                class="form-control @error('transaction_type') is-invalid @enderror" required>
                            <option value="">-- Select Transaction Type --</option>
                            <option value="Deposit" {{ old('transaction_type') === 'Deposit' ? 'selected' : '' }}>
                                Deposit (Credit)
                            </option>
                            <option value="Withdraw" {{ old('transaction_type') === 'Withdraw' ? 'selected' : '' }}>
                                Withdrawal (Debit)
                            </option>
                        </select>
                        @error('transaction_type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Amount --}}
                    <div class="form-group">
                        <label for="amount">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">&#8377;</span>
                            </div>
                            <input type="number" name="amount" id="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}"
                                   placeholder="0.00"
                                   min="1" step="0.01" required>
                        </div>
                        @error('amount')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Transaction Mode --}}
                    <div class="form-group">
                        <label for="transaction_mode">Transaction Mode <span class="text-danger">*</span></label>
                        <select name="transaction_mode" id="transaction_mode"
                                class="form-control @error('transaction_mode') is-invalid @enderror" required>
                            <option value="">-- Select Mode --</option>
                            <option value="Cash" {{ old('transaction_mode') === 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Cheque" {{ old('transaction_mode') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="NEFT" {{ old('transaction_mode') === 'NEFT' ? 'selected' : '' }}>NEFT</option>
                            <option value="RTGS" {{ old('transaction_mode') === 'RTGS' ? 'selected' : '' }}>RTGS</option>
                            <option value="IMPS" {{ old('transaction_mode') === 'IMPS' ? 'selected' : '' }}>IMPS</option>
                        </select>
                        @error('transaction_mode')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Cheque Fields (conditional) --}}
                    <div id="chequeFields" style="display: none;">
                        <div class="card bg-light mb-3">
                            <div class="card-header py-2">
                                <h6 class="card-title mb-0"><i class="fas fa-money-check mr-2"></i>Cheque Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cheque_number">Cheque Number</label>
                                            <input type="text" name="cheque_number" id="cheque_number"
                                                   class="form-control @error('cheque_number') is-invalid @enderror"
                                                   value="{{ old('cheque_number') }}"
                                                   placeholder="Enter cheque number">
                                            @error('cheque_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cheque_date">Cheque Date</label>
                                            <input type="date" name="cheque_date" id="cheque_date"
                                                   class="form-control @error('cheque_date') is-invalid @enderror"
                                                   value="{{ old('cheque_date') }}">
                                            @error('cheque_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank_name">Bank Name</label>
                                            <input type="text" name="bank_name" id="bank_name"
                                                   class="form-control @error('bank_name') is-invalid @enderror"
                                                   value="{{ old('bank_name') }}"
                                                   placeholder="Issuing bank name">
                                            @error('bank_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="branch_name">Branch Name</label>
                                            <input type="text" name="branch_name" id="branch_name"
                                                   class="form-control @error('branch_name') is-invalid @enderror"
                                                   value="{{ old('branch_name') }}"
                                                   placeholder="Issuing bank branch">
                                            @error('branch_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="ifsc_code">IFSC Code</label>
                                            <input type="text" name="ifsc_code" id="ifsc_code"
                                                   class="form-control @error('ifsc_code') is-invalid @enderror"
                                                   value="{{ old('ifsc_code') }}"
                                                   placeholder="IFSC code"
                                                   style="text-transform: uppercase;">
                                            @error('ifsc_code')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Remarks --}}
                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="2"
                                  class="form-control @error('remarks') is-invalid @enderror"
                                  placeholder="Optional remarks for this transaction">{{ old('remarks') }}</textarea>
                        @error('remarks')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-check-circle mr-1"></i> Process Transaction
                    </button>
                    <a href="{{ route('cashier.transactions.index') }}" class="btn btn-default ml-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle cheque fields based on transaction mode
    $('#transaction_mode').on('change', function () {
        if ($(this).val() === 'Cheque') {
            $('#chequeFields').slideDown();
            $('#cheque_number, #cheque_date').attr('required', true);
        } else {
            $('#chequeFields').slideUp();
            $('#cheque_number, #cheque_date').removeAttr('required');
        }
    });

    // Re-trigger on page load in case of old input
    if ($('#transaction_mode').val() === 'Cheque') {
        $('#chequeFields').show();
    }

    // Account number search
    $('#searchAccount').on('click', function () {
        var accountNumber = $('#account_number').val().trim();
        if (!accountNumber) {
            alert('Please enter an account number.');
            return;
        }

        var btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '{{ route("cashier.transactions.create") }}',
            method: 'GET',
            data: { lookup_account: accountNumber },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.success) {
                    $('#customer_name_display').val(response.customer_name);
                    $('#balance_display').val(parseFloat(response.balance).toFixed(2));
                    $('#account_type_display').val(response.account_type);
                    $('#account_status_display').val(response.status);
                    $('#bank_account_id').val(response.account_id);
                    $('#accountDetails').slideDown();
                } else {
                    alert(response.message || 'Account not found.');
                    $('#accountDetails').hide();
                    $('#bank_account_id').val('');
                }
            },
            error: function () {
                alert('Error searching account. Please try again.');
                $('#accountDetails').hide();
            },
            complete: function () {
                btn.html('<i class="fas fa-search"></i> Search').prop('disabled', false);
            }
        });
    });

    // Allow pressing Enter in account number field to trigger search
    $('#account_number').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#searchAccount').click();
        }
    });
</script>
@endpush
