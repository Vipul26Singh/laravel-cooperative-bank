<?php
namespace App\Services;
use App\Models\{FdAccount, FdTransaction, InitializeAccountNumber};
use App\Events\{FdAccountOpened};
use Illuminate\Support\Facades\DB;

class FdService
{
    public function computeMaturityAmount(float $principal, float $rate, int $days): float
    {
        return round($principal * (1 + ($rate / 100) * ($days / 365)), 2);
    }

    public function generateFdNumber(int $branchId): int
    {
        return DB::transaction(function () use ($branchId) {
            $init = InitializeAccountNumber::where('branch_id', $branchId)->lockForUpdate()->firstOrFail();
            $number = $init->fd_account_start;
            $init->increment('fd_account_start');
            return $number;
        });
    }

    public function openFd(array $data): FdAccount
    {
        return DB::transaction(function () use ($data) {
            $maturity = $this->computeMaturityAmount($data['principal_amount'], $data['interest_rate'], $data['duration_days']);
            $data['fd_number']      = $this->generateFdNumber($data['branch_id']);
            $data['maturity_amount'] = $maturity;
            $data['maturity_date']  = \Carbon\Carbon::parse($data['fd_date'])->addDays($data['duration_days']);
            $fd = FdAccount::create($data);
            // Record opening transaction
            FdTransaction::create([
                'fd_account_id'    => $fd->id,
                'fd_number'        => $fd->fd_number,
                'customer_id'      => $fd->customer_id,
                'transaction_type' => 'Deposit',
                'amount'           => $fd->principal_amount,
                'interest_earned'  => 0,
                'balance_after'    => $fd->principal_amount,
                'transaction_date' => $fd->fd_date,
                'branch_id'        => $fd->branch_id,
                'created_by'       => $fd->created_by,
            ]);
            event(new FdAccountOpened($fd));
            return $fd;
        });
    }

    public function processMature(FdAccount $fd): FdTransaction
    {
        return DB::transaction(function () use ($fd) {
            $fd->update(['is_matured' => true]);
            return FdTransaction::create([
                'fd_account_id'    => $fd->id,
                'fd_number'        => $fd->fd_number,
                'customer_id'      => $fd->customer_id,
                'transaction_type' => 'Maturity',
                'amount'           => $fd->maturity_amount,
                'interest_earned'  => $fd->maturity_amount - $fd->principal_amount,
                'balance_after'    => $fd->maturity_amount,
                'transaction_date' => now(),
                'branch_id'        => $fd->branch_id,
                'created_by'       => $fd->created_by,
            ]);
        });
    }
}
