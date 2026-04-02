<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('contract_type', ['pkwt', 'pkwtt', 'internship', 'freelance'])->default('pkwtt');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_probation')->default(false);
            $table->tinyInteger('probation_months')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_contracts');
    }
};
