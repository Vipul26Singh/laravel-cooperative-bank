<?php
namespace App\Listeners;
use App\Events\LoanDisbursed;
use App\Services\LoanService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateLoanInstallmentSchedule implements ShouldQueue
{
    use InteractsWithQueue;
    public string $queue = 'banking';

    public function __construct(private LoanService $loanService) {}

    public function handle(LoanDisbursed $event): void
    {
        // Schedule is computed on-the-fly from loan params, but we can log/notify
        $loan = $event->loan;
        // Notify customer of disbursement and schedule
        if ($loan->customer->email) {
            \App\Jobs\SendEmailJob::dispatch(
                to: $loan->customer->email,
                subject: 'Loan Disbursed - Account No: ' . $loan->loan_number,
                template: 'emails.loan.disbursed',
                data: ['loan' => $loan]
            );
        }
    }
}
