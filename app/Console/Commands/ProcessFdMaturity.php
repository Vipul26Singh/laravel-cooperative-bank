<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFdMaturityJob;
use Illuminate\Console\Command;

class ProcessFdMaturity extends Command
{
    protected $signature = 'bank:process-fd-maturity';
    protected $description = 'Check and mark matured FD accounts, dispatch maturity events';

    public function handle(): void
    {
        $this->info('Processing FD maturities...');
        ProcessFdMaturityJob::dispatch();
        $this->info('FD maturity job dispatched.');
    }
}
