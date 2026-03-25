<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private string $to,
        private string $message
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $config = config('services.sms');

        if (empty($config['url'])) {
            Log::warning('SMS service URL not configured. Skipping SMS to: ' . $this->to);
            return;
        }

        $payload = array_merge($config['params'] ?? [], [
            $config['mobile_param'] ?? 'mobile'   => $this->to,
            $config['message_param'] ?? 'message' => $this->message,
        ]);

        $response = Http::timeout(30)
            ->withHeaders($config['headers'] ?? [])
            ->post($config['url'], $payload);

        if (!$response->successful()) {
            Log::error('SMS sending failed', [
                'to'       => $this->to,
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);

            $this->fail(new \Exception('SMS API returned status: ' . $response->status()));
        }
    }
}
