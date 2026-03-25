<?php

namespace App\Console\Commands;

use App\Jobs\ProcessLoanOdInterestJob;
use Illuminate\Console\Command;

class ProcessLoanOdInterest extends Command
{
    protected $signature = 'bank:process-loan-od-interest';
    protected $description = 'Calculate and update OD interest for overdue loans';

    public function handle(): void
    {
        $this->info('Processing OD interest for overdue loans...');
        ProcessLoanOdInterestJob::dispatch();
        $this->info('OD interest job dispatched.');
    }
}
