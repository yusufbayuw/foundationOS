<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_slims_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('entity_type', 32);
            $table->string('slims_id', 64);
            $table->unsignedBigInteger('fos_id');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'entity_type', 'slims_id'], 'slims_map_tenant_entity_slims_unique');
            $table->unique(['tenant_id', 'entity_type', 'fos_id'], 'slims_map_tenant_entity_fos_unique');
            $table->index(['tenant_id', 'entity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_slims_mappings');
    }
};
