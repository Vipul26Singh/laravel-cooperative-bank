<?php
namespace App\Jobs;
use App\Models\Loan;
use App\Models\LoanSetting;
use App\Models\LoanTransaction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessLoanOdInterestJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $loanSetting = LoanSetting::first();
        $odRate      = $loanSetting?->od_interest_rate ?? 2.0; // default 2% per month

        Loan::where('status', 'active')
            ->where('outstanding_balance', '>', 0)
            ->each(function (Loan $loan) use ($odRate) {
                try {
                    $this->processLoanOd($loan, $odRate);
                } catch (\Throwable $e) {
                    Log::error('OD interest processing failed for loan: ' . $loan->loan_number, [
                        'error' => $e->getMessage(),
                    ]);
                }
            });
    }

    private function processLoanOd(Loan $loan, float $odRate): void
    {
        // Determine which installment is currently due
        $installmentAmount = $loan->installment_amount;
        $numInstallments   = $loan->num_installments;
        $firstDueDate      = $loan->first_installment_date ?? $loan->disbursement_date?->addMonth() ?? now();
        $frequency         = $loan->frequency ?? 'MONTHLY';

        $today = Carbon::today();

        // Build expected due dates up to today
        $dueDates = [];
        $dueDate  = Carbon::parse($firstDueDate);

        for ($i = 1; $i <= $numInstallments; $i++) {
            if ($dueDate->lte($today)) {
                $dueDates[] = [
                    'installment_no' => $i,
                    'due_date'       => $dueDate->copy(),
                ];
            }
            $dueDate = match ($frequency) {
                'DAILY'  => $dueDate->addDay(),
                'WEEKLY' => $dueDate->addWeek(),
                default  => $dueDate->addMonth(),
            };
        }

        if (empty($dueDates)) {
            return;
        }

        // Count how many installments have been paid
        $paidCount = LoanTransaction::where('loan_id', $loan->id)
            ->where('transaction_type', 'Repayment')
            ->count();

        // Determine overdue installments
        $overdueInstallments = count($dueDates) - $paidCount;

        if ($overdueInstallments <= 0) {
            // All due installments paid, reset OD interest
            if ($loan->od_interest_amount > 0) {
                $loan->update(['od_interest_amount' => 0]);
            }
            return;
        }

        // Calculate OD interest on outstanding balance
        $monthlyOdRate = $odRate / 100;
        $odInterest    = round($loan->outstanding_balance * $monthlyOdRate * $overdueInstallments, 2);

        DB::transaction(function () use ($loan, $odInterest) {
            $loan->update(['od_interest_amount' => $odInterest]);
        });

        Log::info('OD interest calculated for loan: ' . $loan->loan_number, [
            'overdue_installments' => $overdueInstallments,
            'od_interest'          => $odInterest,
        ]);
    }
}
