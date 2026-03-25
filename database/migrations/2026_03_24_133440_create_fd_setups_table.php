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
        Schema::create('fd_setups', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->decimal('interest_rate', 5, 2);
            $table->unsignedInteger('duration_days');
            $table->boolean('is_senior_citizen')->default(false);
            $table->boolean('is_special_roi')->default(false);
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('fd_setups');
    }
};
