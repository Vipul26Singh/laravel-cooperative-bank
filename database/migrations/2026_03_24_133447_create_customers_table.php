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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_number')->unique();
            $table->string('full_name', 255);
            $table->date('dob')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('spouse_name', 100)->nullable();
            $table->date('spouse_dob')->nullable();
            $table->text('residential_address');
            $table->text('office_address')->nullable();
            $table->string('pincode', 20)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('mobile', 20);
            $table->string('email', 255)->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('father_name', 100)->nullable();
            $table->string('family_details', 255)->nullable();
            $table->string('family_relation', 100)->nullable();
            $table->string('nominee_name', 100)->nullable();
            $table->string('nominee_age', 50)->nullable();
            $table->string('nominee_relation', 100)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('caste', 50)->nullable();
            $table->string('pan_number', 25)->nullable();
            $table->string('uid_number', 50)->nullable();
            $table->string('id_type', 50)->nullable();
            $table->string('photo_identity_number', 50)->nullable();
            $table->longText('photo')->nullable();
            $table->longText('photo_id')->nullable();
            $table->longText('id_proof1')->nullable();
            $table->longText('id_proof2')->nullable();
            $table->longText('signature')->nullable();
            $table->decimal('membership_fee', 10, 2)->default(0);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->boolean('is_member_active')->default(false);
            $table->date('activation_date')->nullable();
            $table->string('approval_status', 20)->default('pending'); // pending, approved, rejected
            $table->dateTime('approval_date')->nullable();
            $table->text('approver_remark')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('customers');
    }
};
