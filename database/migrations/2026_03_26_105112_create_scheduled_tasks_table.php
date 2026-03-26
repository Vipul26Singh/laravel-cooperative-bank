<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('command', 255);
            $table->string('frequency', 50);
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_run_at')->nullable();
            $table->dateTime('next_run_at')->nullable();
            $table->string('last_status', 20)->nullable();
            $table->text('last_output')->nullable();
            $table->unsignedInteger('run_count')->default(0);
            $table->unsignedInteger('fail_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_tasks');
    }
};
