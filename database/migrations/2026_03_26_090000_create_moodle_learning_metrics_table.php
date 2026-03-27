<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moodle_learning_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('fos_user_id')->nullable();
            $table->unsignedBigInteger('fos_course_id')->nullable();
            $table->unsignedBigInteger('moodle_user_id')->nullable();
            $table->unsignedBigInteger('moodle_course_id')->nullable();
            $table->string('metric_type', 32);
            $table->json('payload');
            $table->timestamp('pulled_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'metric_type']);
            $table->index(['fos_user_id', 'fos_course_id', 'metric_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moodle_learning_metrics');
    }
};

