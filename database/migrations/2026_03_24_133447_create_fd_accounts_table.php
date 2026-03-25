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
        Schema::create('fd_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fd_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('fd_setup_id')->constrained('fd_setups')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->unsignedInteger('duration_days');
            $table->dateTime('fd_date');
            $table->decimal('maturity_amount', 15, 2);
            $table->date('maturity_date');
            $table->dateTime('withdrawal_date')->nullable();
            $table->boolean('is_matured')->default(false);
            $table->boolean('is_withdrawn')->default(false);
            $table->string('transaction_mode', 20)->default('cash');
            $table->string('cheque_number', 50)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->date('cheque_date')->nullable();
            $table->boolean('via_mobile')->default(false);
            $table->boolean('via_internet')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fd_accounts');
    }
};
