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
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('loan_type_id')->constrained('loan_types')->onDelete('restrict');
            $table->decimal('applied_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->date('application_date');
            $table->string('loan_purpose', 255);
            $table->unsignedInteger('duration_months');
            $table->string('frequency', 20)->default('MONTHLY'); // DAILY, WEEKLY, MONTHLY
            $table->unsignedBigInteger('guarantor1_id')->nullable();
            $table->foreign('guarantor1_id')->references('id')->on('customers')->nullOnDelete();
            $table->unsignedBigInteger('guarantor2_id')->nullable();
            $table->foreign('guarantor2_id')->references('id')->on('customers')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->string('approval_status', 20)->default('pending'); // pending, approved, rejected
            $table->dateTime('approval_date')->nullable();
            $table->text('approver_remark')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('loan_status', 20)->default('pending'); // pending, allotted, rejected
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->boolean('via_mobile')->default(false);
            $table->boolean('via_internet')->default(false);
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
        Schema::dropIfExists('loan_applications');
    }
};
