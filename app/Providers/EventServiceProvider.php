<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\{CustomerRegistered, CustomerApproved, AccountOpened, TransactionCompleted, LoanApplicationSubmitted, LoanApproved, LoanDisbursed, LoanRepaymentRecorded, FdAccountOpened, FdMatured, ShareTransactionCompleted};
use App\Listeners\{SendCustomerWelcomeNotification, SendCustomerApprovalNotification, GenerateLoanInstallmentSchedule, SendLoanApprovalNotification, SendRepaymentReceipt, SendFdOpeningConfirmation, SendFdMaturityAlert, LogAuditTrail};

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CustomerRegistered::class => [
            SendCustomerWelcomeNotification::class,
            LogAuditTrail::class,
        ],
        CustomerApproved::class => [
            SendCustomerApprovalNotification::class,
            LogAuditTrail::class,
        ],
        AccountOpened::class => [
            LogAuditTrail::class,
        ],
        TransactionCompleted::class => [
            LogAuditTrail::class,
        ],
        LoanApplicationSubmitted::class => [
            LogAuditTrail::class,
        ],
        LoanApproved::class => [
            SendLoanApprovalNotification::class,
            LogAuditTrail::class,
        ],
        LoanDisbursed::class => [
            GenerateLoanInstallmentSchedule::class,
            LogAuditTrail::class,
        ],
        LoanRepaymentRecorded::class => [
            SendRepaymentReceipt::class,
            LogAuditTrail::class,
        ],
        FdAccountOpened::class => [
            SendFdOpeningConfirmation::class,
            LogAuditTrail::class,
        ],
        FdMatured::class => [
            SendFdMaturityAlert::class,
            LogAuditTrail::class,
        ],
        ShareTransactionCompleted::class => [
            LogAuditTrail::class,
        ],
    ];

    public function boot(): void {}

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
