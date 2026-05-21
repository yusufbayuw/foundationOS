<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moodle_offering_mappings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('course_offering_id')->nullable();
            $table->string('kind', 16); // 'template' | 'offering'
            $table->unsignedBigInteger('moodle_id')->nullable();
            $table->string('idnumber', 191);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique('idnumber', 'moodle_offering_idnumber_unique');
            $table->index(['tenant_id', 'kind'], 'moodle_offering_tenant_kind_idx');
            $table->index('course_id', 'moodle_offering_course_idx');
            $table->index('course_offering_id', 'moodle_offering_offering_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moodle_offering_mappings');
    }
};
