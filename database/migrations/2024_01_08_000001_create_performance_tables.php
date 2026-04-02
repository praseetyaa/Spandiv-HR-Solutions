<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['annual', 'mid_year', 'quarterly', 'probation'])->default('annual');
            $table->boolean('is_360')->default(false);
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('review_cycles')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users');
            $table->enum('reviewer_type', ['self', 'manager', 'peer', 'subordinate'])->default('manager');
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('rating')->nullable();
            $table->text('summary')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'acknowledged'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'cycle_id']);
        });

        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('review_cycles')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('metric_unit')->nullable();
            $table->decimal('target', 10, 2);
            $table->decimal('actual', 10, 2)->default(0);
            $table->integer('weight_percent')->default(100);
            $table->enum('status', ['draft', 'active', 'achieved', 'missed'])->default('draft');
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });

        Schema::create('talent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('potential_level', ['low', 'medium', 'high', 'very_high'])->default('medium');
            $table->enum('performance_level', ['below', 'meets', 'exceeds', 'outstanding'])->default('meets');
            $table->enum('flight_risk', ['low', 'medium', 'high'])->default('low');
            $table->boolean('is_successor_ready')->default(false);
            $table->text('strengths')->nullable();
            $table->text('development_notes')->nullable();
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();

            $table->unique(['tenant_id', 'employee_id']);
        });

        Schema::create('succession_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('position_id')->constrained('job_positions');
            $table->foreignId('candidate_employee_id')->constrained('employees');
            $table->integer('readiness_level')->default(3);
            $table->integer('priority')->default(1);
            $table->text('development_notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['tenant_id', 'position_id']);
        });

        Schema::create('career_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('from_position_id')->constrained('job_positions');
            $table->foreignId('to_position_id')->constrained('job_positions');
            $table->enum('path_type', ['vertical', 'lateral', 'diagonal'])->default('vertical');
            $table->integer('avg_years_required');
            $table->text('requirements')->nullable();
            $table->timestamps();
        });

        Schema::create('idp_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('review_id')->nullable()->constrained('performance_reviews')->nullOnDelete();
            $table->year('year');
            $table->text('development_focus');
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('idp_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idp_id')->constrained('idp_plans')->cascadeOnDelete();
            $table->enum('activity_type', ['training', 'mentoring', 'project', 'course', 'certification', 'other']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('target_date');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->text('outcome')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idp_activities');
        Schema::dropIfExists('idp_plans');
        Schema::dropIfExists('career_paths');
        Schema::dropIfExists('succession_plans');
        Schema::dropIfExists('talent_profiles');
        Schema::dropIfExists('goals');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('review_cycles');
    }
};
