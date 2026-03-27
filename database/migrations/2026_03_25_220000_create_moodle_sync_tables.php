<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moodle_sync_outbox', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 32);
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('action', 32);
            $table->json('payload')->nullable();
            $table->string('dedupe_key')->unique();
            $table->string('status', 16)->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'next_retry_at']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('tenant_id');
        });

        Schema::create('moodle_entity_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 32);
            $table->unsignedBigInteger('fos_entity_id');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('moodle_id')->nullable();
            $table->string('moodle_idnumber', 191);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['entity_type', 'fos_entity_id']);
            $table->unique(['entity_type', 'moodle_idnumber']);
            $table->index('tenant_id');
        });

        Schema::create('moodle_class_course_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('moodle_course_id')->nullable();
            $table->string('moodle_course_idnumber', 191)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'class_id']);
            $table->index('course_id');
            $table->index('moodle_course_idnumber');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moodle_class_course_mappings');
        Schema::dropIfExists('moodle_entity_mappings');
        Schema::dropIfExists('moodle_sync_outbox');
    }
};

