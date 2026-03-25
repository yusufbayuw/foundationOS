<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('tuition_type_id')->nullable()->constrained('tuition_types')->nullOnDelete();
            $table->string('invoice_number');
            $table->string('invoice_type')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->text('discount_reason')->nullable();
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);
            $table->string('status')->default('draft');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->string('sent_via')->nullable();
            $table->morphs('invoiceable');
            $table->unique(['tenant_id', 'invoice_number']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_invoices');
    }
};
