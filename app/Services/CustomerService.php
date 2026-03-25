<?php
namespace App\Services;
use App\Models\{Customer, InitializeAccountNumber, MembershipFee, ShareAccount};
use App\Events\{CustomerRegistered, CustomerApproved};
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function generateCustomerNumber(): int
    {
        // Use atomic increment on a separate sequence
        return DB::transaction(function () {
            $max = Customer::max('customer_number') ?? 1000;
            return $max + 1;
        });
    }

    public function register(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $data['customer_number']  = $this->generateCustomerNumber();
            $data['approval_status']  = 'pending';
            $data['is_member_active'] = false;
            $customer = Customer::create($data);
            // Auto-create share account stub
            event(new CustomerRegistered($customer));
            return $customer;
        });
    }

    public function approve(Customer $customer, int $approvedBy, string $remark = ''): Customer
    {
        return DB::transaction(function () use ($customer, $approvedBy, $remark) {
            $customer->update([
                'approval_status'  => 'approved',
                'is_member_active' => true,
                'approved_by'      => $approvedBy,
                'approval_date'    => now(),
                'approver_remark'  => $remark,
                'activation_date'  => now(),
            ]);
            event(new CustomerApproved($customer->fresh(), auth()->user()));
            return $customer->fresh();
        });
    }

    public function reject(Customer $customer, int $rejectedBy, string $remark): Customer
    {
        $customer->update([
            'approval_status' => 'rejected',
            'approved_by'     => $rejectedBy,
            'approval_date'   => now(),
            'approver_remark' => $remark,
        ]);
        return $customer->fresh();
    }
}
