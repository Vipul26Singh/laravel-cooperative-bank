<?php
namespace App\Services;
use App\Models\{Loan, LoanApplication, LoanTransaction, LoanSetting, InitializeAccountNumber};
use App\Events\{LoanApplicationSubmitted, LoanApproved, LoanDisbursed, LoanRepaymentRecorded};
use Illuminate\Support\Facades\DB;

class LoanService
{
    public function calculateEmi(float $principal, float $annualRate, int $months): float
    {
        if ($annualRate == 0) return round($principal / $months, 2);
        $r = $annualRate / 12 / 100;
        $n = $months;
        $emi = $principal * $r * pow(1 + $r, $n) / (pow(1 + $r, $n) - 1);
        return round($emi, 2);
    }

    public function generateLoanNumber(int $branchId): int
    {
        return DB::transaction(function () use ($branchId) {
            $init = InitializeAccountNumber::where('branch_id', $branchId)->lockForUpdate()->firstOrFail();
            $number = $init->loan_account_start;
            $init->increment('loan_account_start');
            return $number;
        });
    }

    public function submitApplication(array $data): LoanApplication
    {
        return DB::transaction(function () use ($data) {
            $data['application_date'] = $data['application_date'] ?? now()->toDateString();
            $application = LoanApplication::create($data);
            event(new LoanApplicationSubmitted($application));
            return $application;
        });
    }

    public function approveApplication(LoanApplication $application, array $data): LoanApplication
    {
        return DB::transaction(function () use ($application, $data) {
            $application->update([
                'approval_status'  => 'approved',
                'approved_amount'  => $data['approved_amount'],
                'approved_by'      => $data['approved_by'],
                'approval_date'    => now(),
                'approver_remark'  => $data['remark'] ?? null,
                'loan_status'      => 'allotted',
            ]);
            event(new LoanApproved($application, auth()->user()));
            return $application->fresh();
        });
    }

    public function rejectApplication(LoanApplication $application, array $data): LoanApplication
    {
        $application->update([
            'approval_status' => 'rejected',
            'approved_by'     => $data['approved_by'],
            'approval_date'   => now(),
            'approver_remark' => $data['remark'] ?? null,
        ]);
        return $application->fresh();
    }

    public function disburseLoan(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            $emi = $this->calculateEmi($data['amount'], $data['interest_rate'], $data['duration_months']);
            $data['loan_number']         = $this->generateLoanNumber($data['branch_id']);
            $data['installment_amount']  = $emi;
            $data['num_installments']    = $data['duration_months'];
            $data['outstanding_balance'] = $data['amount'];
            $data['loan_date']           = $data['loan_date'] ?? now()->toDateString();
            $data['disburse_date']       = $data['disburse_date'] ?? now();
            $data['status']              = 'active';
            $loan = Loan::create($data);
            if (isset($data['loan_application_id'])) {
                LoanApplication::where('id', $data['loan_application_id'])
                    ->update(['loan_status' => 'allotted']);
            }
            event(new LoanDisbursed($loan));
            return $loan;
        });
    }

    public function recordRepayment(Loan $loan, array $data): LoanTransaction
    {
        return DB::transaction(function () use ($loan, $data) {
            $newBalance = $loan->outstanding_balance - $data['principal_paid'];
            $loan->update(['outstanding_balance' => $newBalance, 'od_interest_amount' => 0]);
            if ($newBalance <= 0) {
                $loan->update(['status' => 'closed', 'outstanding_balance' => 0]);
            }
            $transaction = $loan->transactions()->create([
                ...$data,
                'outstanding_balance_after' => max(0, $newBalance),
                'loan_number'              => $loan->loan_number,
                'customer_id'              => $loan->customer_id,
            ]);
            event(new LoanRepaymentRecorded($transaction));
            return $transaction;
        });
    }

    public function getInstallmentSchedule(Loan $loan): array
    {
        $schedule = [];
        $balance  = $loan->amount;
        $r        = $loan->interest_rate / 12 / 100;
        $emi      = $loan->installment_amount;
        $date     = $loan->first_installment_date ?? now()->addMonth();
        $freq     = $loan->frequency;

        for ($i = 1; $i <= $loan->num_installments; $i++) {
            $interest   = round($balance * $r, 2);
            $principal  = round($emi - $interest, 2);
            $balance    = round($balance - $principal, 2);
            $schedule[] = [
                'installment_no' => $i,
                'due_date'       => $date,
                'emi'            => $emi,
                'principal'      => $principal,
                'interest'       => $interest,
                'balance'        => max(0, $balance),
            ];
            $date = match($freq) {
                'DAILY'  => \Carbon\Carbon::parse($date)->addDay(),
                'WEEKLY' => \Carbon\Carbon::parse($date)->addWeek(),
                default  => \Carbon\Carbon::parse($date)->addMonth(),
            };
        }
        return $schedule;
    }
}
