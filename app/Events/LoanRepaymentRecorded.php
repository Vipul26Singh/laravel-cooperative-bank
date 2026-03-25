<?php
namespace App\Events;
use App\Models\LoanTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class LoanRepaymentRecorded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public readonly LoanTransaction $transaction) {}
}
