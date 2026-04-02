<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->integer('days_per_year')->default(12);
            $table->boolean('is_paid')->default(true);
            $table->boolean('carry_over')->default(false);
            $table->integer('max_carry_days')->default(0);
            $table->boolean('requires_attachment')->default(false);
            $table->integer('min_notice_days')->default(0);
            $table->integer('max_consecutive_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
