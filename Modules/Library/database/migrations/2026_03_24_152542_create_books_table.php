<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('book_category_id')->nullable()->constrained('book_categories')->nullOnDelete();
            $table->string('isbn')->nullable();
            $table->string('isbn13')->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->json('authors');
            $table->string('publisher')->nullable();
            $table->string('publication_year')->nullable();
            $table->string('publication_place')->nullable();
            $table->string('edition')->nullable();
            $table->string('volume')->nullable();
            $table->string('series')->nullable();
            $table->string('language')->default('Indonesian');
            $table->unsignedInteger('pages')->nullable();
            $table->string('dimensions')->nullable();
            $table->unsignedInteger('weight_grams')->nullable();
            $table->string('binding_type')->nullable();
            $table->string('classification_code')->nullable();
            $table->json('keywords')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('preview_url')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('source')->nullable();
            $table->unsignedInteger('total_copies')->default(0);
            $table->unsignedInteger('available_copies')->default(0);
            $table->string('location_shelf')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_reference_only')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
