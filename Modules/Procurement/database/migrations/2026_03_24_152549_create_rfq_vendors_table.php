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
        Schema::create('rfq_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('request_for_quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->date('invitation_date')->nullable();
            $table->date('response_deadline')->nullable();
            $table->string('status')->default('invited');
            $table->timestamp('responded_at')->nullable();
            $table->decimal('quotation_amount', 18, 2)->nullable();
            $table->string('quotation_document')->nullable();
            $table->decimal('technical_score', 8, 2)->nullable();
            $table->decimal('price_score', 8, 2)->nullable();
            $table->decimal('total_score', 8, 2)->nullable();
            $table->unsignedInteger('ranking')->nullable();
            $table->boolean('is_shortlisted')->default(false);
            $table->boolean('is_awarded')->default(false);
            $table->text('award_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['request_for_quotation_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_vendors');
    }
};
