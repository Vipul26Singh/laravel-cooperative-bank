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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->constrained('roles')->onDelete('restrict');
            $table->foreignId('branch_id')->after('role_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('employee_code', 50)->unique()->nullable()->after('branch_id');
            $table->string('designation', 100)->nullable()->after('employee_code');
            $table->unsignedBigInteger('employee_id')->nullable()->after('designation');
            $table->unsignedBigInteger('under_of_id')->nullable()->after('employee_id');
            $table->foreign('under_of_id')->references('id')->on('users')->nullOnDelete();
            $table->longText('profile_photo')->nullable()->after('under_of_id');
            $table->boolean('is_active')->default(true)->after('profile_photo');
            $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            $table->unsignedBigInteger('modified_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['under_of_id']);
            $table->dropColumn([
                'role_id',
                'branch_id',
                'employee_code',
                'designation',
                'employee_id',
                'under_of_id',
                'profile_photo',
                'is_active',
                'created_by',
                'modified_by',
            ]);
        });
    }
};
