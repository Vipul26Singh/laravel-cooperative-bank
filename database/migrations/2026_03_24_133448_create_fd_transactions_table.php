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
        Schema::create('fd_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fd_account_id')->constrained('fd_accounts')->onDelete('restrict');
            $table->unsignedBigInteger('fd_number');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->string('transaction_type', 20); // Deposit, Withdrawal, Interest
            $table->decimal('amount', 15, 2);
            $table->decimal('interest_earned', 15, 2)->default(0);
            $table->decimal('balance_after', 15, 2);
            $table->dateTime('transaction_date');
            $table->text('remarks')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fd_transactions');
    }
};
