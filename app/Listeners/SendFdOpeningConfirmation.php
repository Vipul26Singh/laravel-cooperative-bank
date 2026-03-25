<?php
namespace App\Listeners;
use App\Events\FdAccountOpened;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSmsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFdOpeningConfirmation implements ShouldQueue
{
    use InteractsWithQueue;
    public string $queue = 'notifications';

    public function handle(FdAccountOpened $event): void
    {
        $fd       = $event->fdAccount;
        $customer = $fd->customer;

        if ($customer && $customer->email) {
            SendEmailJob::dispatch(
                to: $customer->email,
                subject: 'Fixed Deposit Account Opened - FD No: ' . $fd->fd_number,
                template: 'emails.fd.opened',
                data: [
                    'fd'       => $fd,
                    'customer' => $customer,
                ]
            );
        }

        if ($customer && $customer->mobile) {
            SendSmsJob::dispatch(
                to: $customer->mobile,
                message: 'Dear ' . $customer->name . ', your Fixed Deposit (FD No: ' . $fd->fd_number . ') of Rs. ' . number_format($fd->principal_amount, 2) . ' has been opened. Maturity Date: ' . $fd->maturity_date . '. Cooperative Bank.'
            );
        }
    }
}
