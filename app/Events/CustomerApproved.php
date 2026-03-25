<?php
namespace App\Events;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class CustomerApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(
        public readonly Customer $customer,
        public readonly User $approvedBy
    ) {}
}
