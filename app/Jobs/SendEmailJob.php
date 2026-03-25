<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private string $to,
        private string $subject,
        private string $template,
        private array $data = []
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        Mail::send($this->template, $this->data, function ($message) {
            $message->to($this->to)->subject($this->subject);
        });
    }
}
