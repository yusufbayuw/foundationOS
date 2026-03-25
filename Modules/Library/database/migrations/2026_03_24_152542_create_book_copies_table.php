<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->string('copy_number');
            $table->string('barcode')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->string('acquisition_source')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('condition')->nullable();
            $table->string('status')->default('available');
            $table->string('location_shelf')->nullable();
            $table->text('notes')->nullable();
            $table->unique(['book_id', 'copy_number']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
