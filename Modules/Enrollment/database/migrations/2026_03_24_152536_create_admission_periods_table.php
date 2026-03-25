<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('type')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('announcement_date')->nullable();
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->unsignedInteger('quota')->default(100);
            $table->unsignedInteger('registered_count')->default(0);
            $table->unsignedInteger('accepted_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('requirements')->nullable();
            $table->unique(['tenant_id', 'organization_id', 'code']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_periods');
    }
};
