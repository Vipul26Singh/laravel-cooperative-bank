<?php
namespace App\Services;
use App\Models\{BankAccount, InitializeAccountNumber, BankAccountTransaction};
use App\Events\{AccountOpened, TransactionCompleted};
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function generateAccountNumber(int $branchId): int
    {
        return DB::transaction(function () use ($branchId) {
            $init = InitializeAccountNumber::where('branch_id', $branchId)->lockForUpdate()->firstOrFail();
            $number = $init->bank_account_start;
            $init->increment('bank_account_start');
            return $number;
        });
    }

    public function openAccount(array $data): BankAccount
    {
        return DB::transaction(function () use ($data) {
            $data['account_number'] = $this->generateAccountNumber($data['branch_id']);
            $account = BankAccount::create($data);
            event(new AccountOpened($account));
            return $account;
        });
    }

    public function deposit(BankAccount $account, array $data): BankAccountTransaction
    {
        return DB::transaction(function () use ($account, $data) {
            $newBalance = $account->balance + $data['amount'];
            $account->update(['balance' => $newBalance]);
            $transaction = $account->transactions()->create([
                ...$data,
                'transaction_type' => 'Deposit',
                'balance_after'    => $newBalance,
                'account_number'   => $account->account_number,
                'customer_id'      => $account->customer_id,
            ]);
            event(new TransactionCompleted($transaction));
            return $transaction;
        });
    }

    public function withdraw(BankAccount $account, array $data): BankAccountTransaction
    {
        return DB::transaction(function () use ($account, $data) {
            throw_if($account->balance < $data['amount'], \Exception::class, 'Insufficient balance');
            $newBalance = $account->balance - $data['amount'];
            $account->update(['balance' => $newBalance]);
            $transaction = $account->transactions()->create([
                ...$data,
                'transaction_type' => 'Withdraw',
                'balance_after'    => $newBalance,
                'account_number'   => $account->account_number,
                'customer_id'      => $account->customer_id,
            ]);
            event(new TransactionCompleted($transaction));
            return $transaction;
        });
    }
}
