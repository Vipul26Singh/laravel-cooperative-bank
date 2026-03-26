<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_run_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_task_id')->constrained('scheduled_tasks')->cascadeOnDelete();
            $table->string('status', 20);
            $table->text('output')->nullable();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->unsignedBigInteger('triggered_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_run_logs');
    }
};
