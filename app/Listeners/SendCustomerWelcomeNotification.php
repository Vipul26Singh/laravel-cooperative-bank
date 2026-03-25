<?php
namespace App\Listeners;
use App\Events\CustomerRegistered;
use App\Jobs\SendEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCustomerWelcomeNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public string $queue = 'notifications';

    public function handle(CustomerRegistered $event): void
    {
        $customer = $event->customer;
        if ($customer->email) {
            SendEmailJob::dispatch(
                to: $customer->email,
                subject: 'Welcome to Cooperative Bank',
                template: 'emails.customer.welcome',
                data: ['customer' => $customer]
            );
        }
    }
}
