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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_number')->unique();
            $table->foreignId('loan_application_id')->nullable()->constrained('loan_applications')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('loan_type_id')->constrained('loan_types')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->unsignedInteger('duration_months');
            $table->unsignedInteger('num_installments');
            $table->decimal('installment_amount', 15, 2);
            $table->string('frequency', 20)->default('MONTHLY'); // DAILY, WEEKLY, MONTHLY
            $table->decimal('outstanding_balance', 15, 2);
            $table->date('first_installment_date')->nullable();
            $table->dateTime('disburse_date')->nullable();
            $table->date('loan_date');
            $table->unsignedBigInteger('guarantor1_id')->nullable();
            $table->foreign('guarantor1_id')->references('id')->on('customers')->nullOnDelete();
            $table->unsignedBigInteger('guarantor2_id')->nullable();
            $table->foreign('guarantor2_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreignId('collector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20)->default('regular'); // regular, gold
            $table->foreignId('fd_account_id')->nullable()->constrained('fd_accounts')->nullOnDelete();
            $table->decimal('od_interest_amount', 15, 2)->default(0);
            $table->string('status', 20)->default('active'); // active, closed, default
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
