<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOldAuditLogs extends Command
{
    protected $signature = 'bank:clean-audit-logs {--days=90 : Delete logs older than N days}';
    protected $description = 'Delete audit log entries older than the specified number of days';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deleted = DB::table('audit_logs')->where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$deleted} audit log entries older than {$days} days.");
        return self::SUCCESS;
    }
}
