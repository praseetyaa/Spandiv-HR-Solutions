<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefit_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->enum('category', ['insurance', 'bpjs', 'allowance', 'facility', 'other']);
            $table->text('description')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('benefit_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('benefit_type_id')->constrained('benefit_types')->cascadeOnDelete();
            $table->string('name');
            $table->string('provider')->nullable();
            $table->decimal('coverage_amount', 15, 2)->nullable();
            $table->enum('coverage_type', ['fixed', 'percentage', 'unlimited'])->default('fixed');
            $table->json('details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('benefit_plans')->cascadeOnDelete();
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->decimal('employee_contribution', 15, 2)->default(0);
            $table->decimal('employer_contribution', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->boolean('requires_receipt')->default(true);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expense_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('title');
            $table->decimal('total_amount', 15, 2);
            $table->date('expense_date');
            $table->text('purpose');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'paid'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('expense_requests')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('expense_categories');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('receipt_path')->nullable();
            $table->date('item_date');
            $table->timestamps();
        });

        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('loan_amount', 15, 2);
            $table->integer('installment_months');
            $table->decimal('monthly_deduction', 15, 2);
            $table->date('start_date');
            $table->decimal('remaining_amount', 15, 2);
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('employee_loans')->cascadeOnDelete();
            $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
            $table->integer('installment_number');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
        Schema::dropIfExists('employee_loans');
        Schema::dropIfExists('expense_items');
        Schema::dropIfExists('expense_requests');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('employee_benefits');
        Schema::dropIfExists('benefit_plans');
        Schema::dropIfExists('benefit_types');
    }
};
