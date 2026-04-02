<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('category');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->boolean('requires_acknowledgment')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('tenant_id');
        });

        Schema::create('policy_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('company_policies')->cascadeOnDelete();
            $table->integer('version_number');
            $table->text('content');
            $table->string('file_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->date('effective_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('policy_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('company_policies')->cascadeOnDelete();
            $table->foreignId('version_id')->constrained('policy_versions')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamp('acknowledged_at');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->unique(['version_id', 'employee_id']);
        });

        Schema::create('disciplinary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('type', ['warning', 'suspension', 'termination']);
            $table->enum('level', ['sp1', 'sp2', 'sp3', 'verbal'])->default('verbal');
            $table->text('violation');
            $table->text('action_taken');
            $table->date('incident_date');
            $table->foreignId('issued_by')->constrained('users');
            $table->string('attachment_path')->nullable();
            $table->date('warning_expires_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });

        Schema::create('grievances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('category');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'investigating', 'resolved', 'closed'])->default('open');
            $table->boolean('is_anonymous')->default(false);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('compliance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('category');
            $table->enum('frequency', ['monthly', 'quarterly', 'annually', 'one_time']);
            $table->date('next_due_date');
            $table->foreignId('responsible_id')->constrained('users');
            $table->enum('status', ['pending', 'completed', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_items');
        Schema::dropIfExists('grievances');
        Schema::dropIfExists('disciplinary_records');
        Schema::dropIfExists('policy_acknowledgments');
        Schema::dropIfExists('policy_versions');
        Schema::dropIfExists('company_policies');
    }
};
