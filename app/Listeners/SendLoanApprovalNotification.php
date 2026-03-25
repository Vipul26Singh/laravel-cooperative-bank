<?php
namespace App\Listeners;
use App\Events\LoanApproved;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSmsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLoanApprovalNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public string $queue = 'notifications';

    public function handle(LoanApproved $event): void
    {
        $application = $event->application;
        $customer    = $application->customer;

        if ($customer->email) {
            SendEmailJob::dispatch(
                to: $customer->email,
                subject: 'Loan Application Approved - Cooperative Bank',
                template: 'emails.loan.approved',
                data: [
                    'application' => $application,
                    'customer'    => $customer,
                    'approvedBy'  => $event->approvedBy,
                ]
            );
        }

        if ($customer->mobile) {
            SendSmsJob::dispatch(
                to: $customer->mobile,
                message: 'Dear ' . $customer->name . ', your loan application has been approved for Rs. ' . number_format($application->approved_amount, 2) . '. Cooperative Bank.'
            );
        }
    }
}
