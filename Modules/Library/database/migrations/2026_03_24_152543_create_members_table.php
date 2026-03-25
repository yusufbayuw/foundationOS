<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('member_number');
            $table->string('member_type')->nullable();
            $table->date('joined_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->unsignedInteger('max_books')->default(3);
            $table->unsignedInteger('loan_period_days')->default(7);
            $table->decimal('fine_per_day', 10, 2)->default(1000);
            $table->unsignedInteger('total_loans_count')->default(0);
            $table->unsignedInteger('current_loans_count')->default(0);
            $table->decimal('total_fines', 10, 2)->default(0);
            $table->decimal('unpaid_fines', 10, 2)->default(0);
            $table->string('status')->default('active');
            $table->text('suspension_reason')->nullable();
            $table->date('suspension_until')->nullable();
            $table->text('notes')->nullable();
            $table->unique(['tenant_id', 'member_number']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
