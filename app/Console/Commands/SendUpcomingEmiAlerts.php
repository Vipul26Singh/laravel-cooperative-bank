<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Jobs\SendEmailJob;
use Illuminate\Console\Command;

class SendUpcomingEmiAlerts extends Command
{
    protected $signature = 'bank:send-upcoming-emi-alerts {--days=3 : Days before due date}';
    protected $description = 'Send alerts for EMIs due in the next N days';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dueDate = now()->addDays($days)->toDateString();

        $loans = Loan::where('status', 'active')
            ->whereDate('next_due_date', $dueDate)
            ->with(['customer', 'loanType'])
            ->get();

        $count = 0;
        foreach ($loans as $loan) {
            if ($loan->customer?->email) {
                SendEmailJob::dispatch(
                    $loan->customer->email,
                    'Upcoming EMI Reminder',
                    "Dear {$loan->customer->full_name}, your EMI of {$loan->installment_amount} for loan #{$loan->loan_number} ({$loan->loanType?->name}) is due on {$loan->next_due_date->format('d M Y')}."
                );
                $count++;
            }
        }

        $this->info("Sent {$count} upcoming EMI alerts for loans due in {$days} days.");
        return self::SUCCESS;
    }
}
