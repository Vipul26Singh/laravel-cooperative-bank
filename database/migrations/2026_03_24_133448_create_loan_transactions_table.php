<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('restrict');
            $table->unsignedBigInteger('loan_number')->index();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->decimal('amount_paid', 15, 2);
            $table->decimal('principal_paid', 15, 2)->default(0);
            $table->decimal('interest_paid', 15, 2)->default(0);
            $table->decimal('od_interest_paid', 15, 2)->default(0);
            $table->decimal('penalty_paid', 15, 2)->default(0);
            $table->decimal('outstanding_balance_after', 15, 2);
            $table->date('payment_date');
            $table->date('installment_due_date')->nullable();
            $table->string('transaction_mode', 20)->default('cash'); // cash, cheque
            $table->string('cheque_number', 50)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->date('cheque_date')->nullable();
            $table->unsignedBigInteger('sender_receiver_account')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->boolean('via_mobile')->default(false);
            $table->boolean('via_internet')->default(false);
            $table->string('status', 20)->default('confirmed');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_transactions');
    }
};
