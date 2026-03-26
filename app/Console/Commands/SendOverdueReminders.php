<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Jobs\SendEmailJob;
use Illuminate\Console\Command;

class SendOverdueReminders extends Command
{
    protected $signature = 'bank:send-overdue-reminders';
    protected $description = 'Send email/SMS reminders for overdue loan EMIs';

    public function handle(): int
    {
        $overdueLoans = Loan::where('status', 'active')
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '<', now())
            ->with(['customer', 'loanType'])
            ->get();

        $count = 0;
        foreach ($overdueLoans as $loan) {
            if ($loan->customer?->email) {
                SendEmailJob::dispatch(
                    $loan->customer->email,
                    'Overdue EMI Reminder',
                    "Dear {$loan->customer->full_name}, your EMI of {$loan->installment_amount} for loan #{$loan->loan_number} is overdue. Please pay at the earliest."
                );
                $count++;
            }
        }

        $this->info("Sent {$count} overdue reminders for {$overdueLoans->count()} loans.");
        return self::SUCCESS;
    }
}
