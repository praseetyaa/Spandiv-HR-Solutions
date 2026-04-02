<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('position_id')->constrained('job_positions');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->string('nik')->nullable();
            $table->string('employee_number');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('employment_type', ['permanent', 'contract', 'internship', 'freelance'])->default('permanent');
            $table->date('join_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated', 'resigned'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
            $table->unique(['tenant_id', 'employee_number']);
            $table->unique(['tenant_id', 'nik']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'department_id']);
        });

        // Add FK for departments.head_employee_id now that employees table exists
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('head_employee_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['head_employee_id']);
        });
        Schema::dropIfExists('employees');
    }
};
