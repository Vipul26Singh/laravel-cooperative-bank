<?php
namespace App\Events;
use App\Models\BankAccount;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class AccountOpened
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public readonly BankAccount $account) {}
}
