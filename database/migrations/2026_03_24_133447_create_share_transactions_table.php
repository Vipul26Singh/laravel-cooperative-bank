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
        Schema::create('share_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_account_id')->constrained('share_accounts')->onDelete('restrict');
            $table->unsignedBigInteger('share_account_number');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->string('transaction_type', 20); // Deposit, Withdraw
            $table->decimal('shares_count', 10, 4);
            $table->decimal('share_amount', 15, 2);
            $table->decimal('balance_shares_after', 15, 4);
            $table->date('transaction_date');
            $table->string('transaction_mode', 20);
            $table->string('cheque_number', 50)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->date('cheque_date')->nullable();
            $table->unsignedBigInteger('sender_receiver_account')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->boolean('via_mobile')->default(false);
            $table->boolean('via_internet')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_transactions');
    }
};
