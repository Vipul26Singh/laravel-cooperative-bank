<?php
namespace App\Events;
use App\Models\ShareTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class ShareTransactionCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public readonly ShareTransaction $transaction) {}
}
