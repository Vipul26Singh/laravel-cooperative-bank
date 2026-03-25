<?php
namespace App\Events;
use App\Models\FdAccount;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class FdAccountOpened
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public readonly FdAccount $fdAccount) {}
}
