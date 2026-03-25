<?php
namespace App\Listeners;
use App\Events\FdMatured;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSmsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFdMaturityAlert implements ShouldQueue
{
    use InteractsWithQueue;
    public string $queue = 'notifications';

    public function handle(FdMatured $event): void
    {
        $fd       = $event->fdAccount;
        $customer = $fd->customer;

        if ($customer && $customer->email) {
            SendEmailJob::dispatch(
                to: $customer->email,
                subject: 'Fixed Deposit Matured - FD No: ' . $fd->fd_number,
                template: 'emails.fd.matured',
                data: [
                    'fd'       => $fd,
                    'customer' => $customer,
                ]
            );
        }

        if ($customer && $customer->mobile) {
            SendSmsJob::dispatch(
                to: $customer->mobile,
                message: 'Dear ' . $customer->name . ', your Fixed Deposit (FD No: ' . $fd->fd_number . ') has matured. Maturity Amount: Rs. ' . number_format($fd->maturity_amount, 2) . '. Please visit the branch to withdraw. Cooperative Bank.'
            );
        }
    }
}
