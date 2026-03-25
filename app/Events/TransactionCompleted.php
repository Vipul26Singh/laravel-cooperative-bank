<?php
namespace App\Events;
use App\Models\BankAccountTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class TransactionCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public readonly BankAccountTransaction $transaction) {}
}
