<?php

namespace App\Console\Commands;

use App\Models\{BankAccountTransaction, LoanTransaction};
use Illuminate\Console\Command;

class GenerateDailyReport extends Command
{
    protected $signature = 'bank:daily-report';
    protected $description = 'Generate and log daily transaction summary for all branches';

    public function handle(): int
    {
        $today = now()->toDateString();

        $deposits = BankAccountTransaction::where('transaction_type', 'Deposit')
            ->whereDate('transaction_date', $today)->sum('amount');

        $withdrawals = BankAccountTransaction::where('transaction_type', 'Withdraw')
            ->whereDate('transaction_date', $today)->sum('amount');

        $loanCollections = LoanTransaction::whereDate('payment_date', $today)->sum('amount_paid');

        $txCount = BankAccountTransaction::whereDate('transaction_date', $today)->count();

        $this->info("Daily Report for {$today}:");
        $this->info("  Deposits:         " . number_format($deposits, 2));
        $this->info("  Withdrawals:      " . number_format($withdrawals, 2));
        $this->info("  Loan Collections: " . number_format($loanCollections, 2));
        $this->info("  Transactions:     {$txCount}");

        return self::SUCCESS;
    }
}
