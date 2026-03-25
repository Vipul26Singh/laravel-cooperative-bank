<?php
namespace App\Services;
use App\Models\{ShareAccount, ShareTransaction, InitializeAccountNumber};
use App\Events\ShareTransactionCompleted;
use Illuminate\Support\Facades\DB;

class ShareService
{
    public function generateShareAccountNumber(int $branchId): int
    {
        return DB::transaction(function () use ($branchId) {
            $init = InitializeAccountNumber::where('branch_id', $branchId)->lockForUpdate()->firstOrFail();
            $number = $init->share_account_start;
            $init->increment('share_account_start');
            return $number;
        });
    }

    public function openShareAccount(array $data): ShareAccount
    {
        return DB::transaction(function () use ($data) {
            $data['share_account_number'] = $this->generateShareAccountNumber($data['branch_id']);
            $data['total_shares']         = 0;
            $data['share_amount']         = 0;
            $shareAccount                 = ShareAccount::create($data);
            return $shareAccount;
        });
    }

    public function recordShareTransaction(ShareAccount $shareAccount, array $data): ShareTransaction
    {
        return DB::transaction(function () use ($shareAccount, $data) {
            $transactionType = $data['transaction_type'] ?? 'Deposit';

            if ($transactionType === 'Deposit') {
                $newTotalShares = $shareAccount->total_shares + ($data['num_shares'] ?? 0);
                $newShareAmount = $shareAccount->share_amount + $data['amount'];
            } else {
                // Withdrawal
                throw_if(
                    $shareAccount->share_amount < $data['amount'],
                    \Exception::class,
                    'Insufficient share balance'
                );
                $newTotalShares = $shareAccount->total_shares - ($data['num_shares'] ?? 0);
                $newShareAmount = $shareAccount->share_amount - $data['amount'];
            }

            $shareAccount->update([
                'total_shares' => max(0, $newTotalShares),
                'share_amount' => max(0, $newShareAmount),
            ]);

            $transaction = $shareAccount->transactions()->create([
                ...$data,
                'share_account_id'     => $shareAccount->id,
                'share_account_number' => $shareAccount->share_account_number,
                'customer_id'          => $shareAccount->customer_id,
                'balance_after'        => max(0, $newShareAmount),
                'shares_after'         => max(0, $newTotalShares),
            ]);

            event(new ShareTransactionCompleted($transaction));
            return $transaction;
        });
    }

    public function deposit(ShareAccount $shareAccount, array $data): ShareTransaction
    {
        return $this->recordShareTransaction($shareAccount, array_merge($data, [
            'transaction_type' => 'Deposit',
        ]));
    }

    public function withdraw(ShareAccount $shareAccount, array $data): ShareTransaction
    {
        return $this->recordShareTransaction($shareAccount, array_merge($data, [
            'transaction_type' => 'Withdrawal',
        ]));
    }
}
