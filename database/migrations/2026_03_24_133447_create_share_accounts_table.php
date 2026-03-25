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
        Schema::create('share_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('share_account_number')->unique();
            $table->foreignId('customer_id')->unique()->constrained('customers')->onDelete('restrict');
            $table->decimal('balance_shares', 15, 4)->default(0);
            $table->date('opening_date');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_accounts');
    }
};
