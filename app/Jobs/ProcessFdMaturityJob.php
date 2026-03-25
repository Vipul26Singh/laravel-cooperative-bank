<?php
namespace App\Jobs;
use App\Models\FdAccount;
use App\Events\FdMatured;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessFdMaturityJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        FdAccount::where('is_matured', false)
            ->where('is_withdrawn', false)
            ->whereDate('maturity_date', '<=', now())
            ->each(function (FdAccount $fd) {
                $fd->update(['is_matured' => true]);
                event(new FdMatured($fd));
            });
    }
}
