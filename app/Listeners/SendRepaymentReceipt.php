<?php
namespace App\Listeners;
use App\Events\LoanRepaymentRecorded;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSmsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRepaymentReceipt implements ShouldQueue
{
    use InteractsWithQueue;
    public string $queue = 'notifications';

    public function handle(LoanRepaymentRecorded $event): void
    {
        $transaction = $event->transaction;
        $loan        = $transaction->loan;
        $customer    = $transaction->customer ?? $loan?->customer;

        if ($customer && $customer->email) {
            SendEmailJob::dispatch(
                to: $customer->email,
                subject: 'Loan Repayment Receipt - Loan No: ' . $transaction->loan_number,
                template: 'emails.loan.repayment_receipt',
                data: [
                    'transaction' => $transaction,
                    'loan'        => $loan,
                    'customer'    => $customer,
                ]
            );
        }

        if ($customer && $customer->mobile) {
            SendSmsJob::dispatch(
                to: $customer->mobile,
                message: 'Dear ' . $customer->name . ', your loan repayment of Rs. ' . number_format($transaction->amount ?? 0, 2) . ' has been recorded. Outstanding: Rs. ' . number_format($transaction->outstanding_balance_after, 2) . '. Cooperative Bank.'
            );
        }
    }
}
