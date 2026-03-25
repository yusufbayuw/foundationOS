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
        Schema::create('rfq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('request_for_quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('procurement_item_id')->nullable()->constrained('procurement_items')->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('unit_of_measure')->nullable();
            $table->decimal('estimated_budget', 18, 2)->default(0);
            $table->text('technical_requirements')->nullable();
            $table->json('mandatory_requirements')->nullable();
            $table->json('scoring_criteria')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_items');
    }
};
