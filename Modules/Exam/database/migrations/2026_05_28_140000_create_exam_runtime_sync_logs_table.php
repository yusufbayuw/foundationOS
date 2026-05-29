<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_runtime_sync_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->string('action');
            $table->string('status');
            $table->uuid('runtime_id')->nullable();
            $table->json('request_summary')->nullable();
            $table->json('response_summary')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['exam_definition_id', 'created_at']);
            $table->index(['tenant_id', 'action', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_runtime_sync_logs');
    }
};
