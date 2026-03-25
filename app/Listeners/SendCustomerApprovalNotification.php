<?php
namespace App\Listeners;
use App\Events\CustomerApproved;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSmsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCustomerApprovalNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public string $queue = 'notifications';

    public function handle(CustomerApproved $event): void
    {
        $customer = $event->customer;

        if ($customer->email) {
            SendEmailJob::dispatch(
                to: $customer->email,
                subject: 'Your Membership Has Been Approved - Cooperative Bank',
                template: 'emails.customer.approved',
                data: [
                    'customer'   => $customer,
                    'approvedBy' => $event->approvedBy,
                ]
            );
        }

        if ($customer->mobile) {
            SendSmsJob::dispatch(
                to: $customer->mobile,
                message: 'Dear ' . $customer->name . ', your membership at Cooperative Bank has been approved. Welcome aboard! Customer No: ' . $customer->customer_number
            );
        }
    }
}
