<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psych_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->enum('category', ['personality', 'intelligence', 'arithmetic', 'sjt', 'projective']);
            $table->string('test_type');
            $table->text('description')->nullable();
            $table->text('instructions');
            $table->integer('duration_minutes');
            $table->integer('total_questions');
            $table->decimal('passing_score', 5, 2)->nullable();
            $table->boolean('is_randomize_q')->default(false);
            $table->boolean('is_randomize_opt')->default(false);
            $table->boolean('is_tenant_specific')->default(false);
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->timestamps();
        });

        Schema::create('psych_test_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('psych_tests')->cascadeOnDelete();
            $table->string('name');
            $table->text('instruction')->nullable();
            $table->integer('order_number');
            $table->integer('duration_minutes')->nullable();
            $table->enum('question_type', ['multiple_choice', 'number_series', 'essay', 'number_input', 'ranking']);
            $table->boolean('is_timed_per_q')->default(false);
            $table->integer('questions_to_answer')->nullable();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('psych_tests')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('psych_test_sections')->nullOnDelete();
            $table->enum('type', ['multiple_choice', 'number_series', 'essay', 'number_input', 'true_false']);
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->integer('order_number');
            $table->decimal('points', 5, 2)->default(1);
            $table->integer('time_limit_sec')->nullable();
            $table->string('dimension_key')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->decimal('score_value', 5, 2)->default(0);
            $table->string('dimension_key')->nullable();
            $table->integer('order_number');
            $table->timestamps();
        });

        // Recruitment test requirements link
        Schema::create('psycho_test_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('test_id')->constrained('psych_tests')->cascadeOnDelete();
            $table->boolean('is_mandatory')->default(true);
            $table->decimal('min_passing_score', 5, 2)->nullable();
            $table->enum('trigger_stage', ['screening', 'interview', 'offering'])->default('screening');
            $table->integer('deadline_days')->default(7);
            $table->integer('order_number')->default(1);
            $table->timestamps();
        });

        Schema::create('candidate_test_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('test_id')->constrained('psych_tests')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users');
            $table->string('access_token')->unique();
            $table->timestamp('deadline_at');
            $table->integer('max_attempts')->default(1);
            $table->integer('attempt_count')->default(0);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'expired'])->default('pending');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_test_assignments');
        Schema::dropIfExists('psycho_test_requirements');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('psych_test_sections');
        Schema::dropIfExists('psych_tests');
    }
};
