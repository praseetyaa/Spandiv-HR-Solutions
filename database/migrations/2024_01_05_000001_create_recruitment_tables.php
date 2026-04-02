<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('position_id')->constrained('job_positions');
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->enum('employment_type', ['permanent', 'contract', 'internship', 'freelance'])->default('permanent');
            $table->decimal('salary_min', 15, 2)->nullable();
            $table->decimal('salary_max', 15, 2)->nullable();
            $table->integer('openings')->default(1);
            $table->enum('status', ['draft', 'published', 'closed', 'cancelled'])->default('draft');
            $table->date('close_date')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('job_posting_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_postings')->cascadeOnDelete();
            $table->string('channel');
            $table->string('external_url')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('job_postings')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->enum('stage', ['applied', 'screening', 'interview', 'offering', 'hired', 'rejected'])->default('applied');
            $table->enum('status', ['active', 'withdrawn', 'rejected', 'hired'])->default('active');
            $table->decimal('score', 5, 2)->nullable();
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'job_id', 'stage']);
        });

        Schema::create('candidate_stage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->string('from_stage')->nullable();
            $table->string('to_stage');
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('changed_at');
            $table->timestamps();
        });

        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('interviewer_id')->constrained('users');
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('type', ['phone', 'video', 'onsite', 'panel'])->default('onsite');
            $table->string('location')->nullable();
            $table->string('meeting_url')->nullable();
            $table->enum('result', ['passed', 'failed', 'pending'])->nullable();
            $table->integer('score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('candidate_stage_logs');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('job_posting_channels');
        Schema::dropIfExists('job_postings');
    }
};
