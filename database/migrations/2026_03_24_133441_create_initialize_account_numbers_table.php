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
        Schema::create('initialize_account_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->unique()->constrained('branches')->onDelete('cascade');
            $table->unsignedBigInteger('bank_account_start')->default(1000001);
            $table->unsignedBigInteger('fd_account_start')->default(3000001);
            $table->unsignedBigInteger('share_account_start')->default(2000001);
            $table->unsignedBigInteger('loan_account_start')->default(4000001);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initialize_account_numbers');
    }
};
