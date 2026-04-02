<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('candidate_test_assignments')->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->integer('time_spent_seconds')->nullable();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('browser_fingerprint')->nullable();
            $table->boolean('is_tab_switched')->default(false);
            $table->integer('tab_switch_count')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->enum('finish_method', ['submitted', 'timeout', 'abandoned'])->nullable();
            $table->timestamps();
        });

        Schema::create('candidate_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('candidate_test_sessions')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->decimal('number_input', 10, 2)->nullable();
            $table->integer('time_spent_sec')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->integer('answer_order')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('candidate_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('assignment_id')->unique()->constrained('candidate_test_assignments')->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('candidate_test_sessions')->cascadeOnDelete();
            $table->decimal('raw_score', 8, 2);
            $table->decimal('scaled_score', 5, 2);
            $table->decimal('percentile', 5, 2)->nullable();
            $table->string('grade');
            $table->json('dimension_scores');
            $table->json('dimension_grades')->nullable();
            $table->text('auto_analysis')->nullable();
            $table->text('reviewer_notes')->nullable();
            $table->enum('overall_recommendation', ['highly_recommended', 'recommended', 'not_recommended', 'pending'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('test_dimension_norms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('psych_tests')->cascadeOnDelete();
            $table->string('dimension_key');
            $table->string('dimension_label');
            $table->decimal('score_min', 5, 2);
            $table->decimal('score_max', 5, 2);
            $table->string('grade');
            $table->string('label');
            $table->text('description');
            $table->text('development_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('result_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('result_id')->constrained('candidate_test_results')->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('job_postings')->nullOnDelete();
            $table->enum('fit_level', ['high_fit', 'moderate_fit', 'low_fit']);
            $table->text('fit_analysis');
            $table->text('strengths');
            $table->text('development_areas');
            $table->text('interview_probes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_recommendations');
        Schema::dropIfExists('test_dimension_norms');
        Schema::dropIfExists('candidate_test_results');
        Schema::dropIfExists('candidate_answers');
        Schema::dropIfExists('candidate_test_sessions');
    }
};
