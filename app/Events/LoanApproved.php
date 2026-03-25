<?php
namespace App\Events;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class LoanApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(
        public readonly LoanApplication $application,
        public readonly User $approvedBy
    ) {}
}
