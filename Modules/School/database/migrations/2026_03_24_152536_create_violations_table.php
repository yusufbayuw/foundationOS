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
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('violation_type_id')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date')->nullable();
            $table->string('severity')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->json('witnesses')->nullable();
            $table->json('sanctions')->nullable();
            $table->unsignedInteger('sanction_duration_days')->nullable();
            $table->boolean('parent_notified')->default(false);
            $table->date('parent_meeting_date')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
