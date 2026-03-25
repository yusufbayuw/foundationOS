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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('curriculum_id')->nullable()->constrained('curricula')->nullOnDelete();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('code');
            $table->text('description')->nullable();
            $table->string('grade_level')->nullable();
            $table->unsignedInteger('credits')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->string('subject_group')->nullable();
            $table->boolean('has_practicum')->default(false);
            $table->string('color_code')->nullable();
            $table->string('icon')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->unique(['tenant_id', 'organization_id', 'code']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
