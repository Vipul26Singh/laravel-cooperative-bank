<?php
namespace App\Events;
use App\Models\Loan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class LoanDisbursed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public readonly Loan $loan) {}
}
