<?php
namespace App\Events;
use App\Models\LoanApplication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class LoanApplicationSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public readonly LoanApplication $application) {}
}
