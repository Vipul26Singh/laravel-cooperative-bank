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
        Schema::create('bank_account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('restrict');
            $table->unsignedBigInteger('account_number')->index();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->string('transaction_type', 20); // Deposit, Withdraw
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('transaction_mode', 20); // cash, cheque
            $table->string('cheque_number', 50)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->date('cheque_date')->nullable();
            $table->unsignedBigInteger('sender_receiver_account')->nullable();
            $table->dateTime('transaction_date');
            $table->text('remarks')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->boolean('via_mobile')->default(false);
            $table->boolean('via_internet')->default(false);
            $table->string('otp_confirmed', 50)->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_account_transactions');
    }
};
