<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_programs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->unsignedBigInteger('moodle_course_id')->nullable();
            $table->decimal('fee_amount', 18, 2)->default(0);
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('instructors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->decimal('honor_rate', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('training_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('training_program_id')->constrained('training_programs')->cascadeOnDelete();
            $table->string('code');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->unsignedInteger('capacity')->default(30);
            $table->string('status')->default('open');
            $table->timestamps();
        });

        Schema::create('training_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('training_batch_id')->constrained('training_batches')->cascadeOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('location')->nullable();
            $table->timestamps();
        });

        Schema::create('training_enrollments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('training_batch_id')->constrained('training_batches')->cascadeOnDelete();
            $table->string('participant_name');
            $table->string('participant_email')->nullable();
            $table->string('status')->default('registered');
            $table->timestamps();
        });

        Schema::create('training_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('training_enrollment_id')->constrained('training_enrollments')->cascadeOnDelete();
            $table->decimal('amount', 18, 2);
            $table->string('payment_status')->default('pending');
            $table->string('payment_reference')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('training_certificates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('training_enrollment_id')->constrained('training_enrollments')->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->string('verification_token')->unique();
            $table->timestamp('issued_at');
            $table->timestamps();
        });

        Schema::create('corporate_training_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('client_name');
            $table->foreignId('training_program_id')->constrained('training_programs')->cascadeOnDelete();
            $table->decimal('package_price', 18, 2);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('training_affiliates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('commission_percent', 5, 2)->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('training_affiliate_commissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('training_affiliate_id')->constrained('training_affiliates')->cascadeOnDelete();
            $table->foreignId('training_payment_id')->constrained('training_payments')->cascadeOnDelete();
            $table->decimal('amount', 18, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_affiliate_commissions');
        Schema::dropIfExists('training_affiliates');
        Schema::dropIfExists('corporate_training_packages');
        Schema::dropIfExists('training_certificates');
        Schema::dropIfExists('training_payments');
        Schema::dropIfExists('training_enrollments');
        Schema::dropIfExists('training_sessions');
        Schema::dropIfExists('training_batches');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('training_programs');
    }
};
