<?php
namespace App\Events;
use App\Models\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class CustomerRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public readonly Customer $customer) {}
}
