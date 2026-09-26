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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unique(['tenant_id', 'organization_id', 'code']);
            $table->timestamps();
        });

        if (Schema::hasTable('classes')) {
            Schema::table('classes', function (Blueprint $table): void {
                $table->foreign('department_id')
                    ->references('id')
                    ->on('departments')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('applicants')) {
            Schema::table('applicants', function (Blueprint $table): void {
                $table->foreign('program_choice_1_id')->references('id')->on('departments')->nullOnDelete();
                $table->foreign('program_choice_2_id')->references('id')->on('departments')->nullOnDelete();
                $table->foreign('accepted_program_id')->references('id')->on('departments')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('applicants')) {
            Schema::table('applicants', function (Blueprint $table): void {
                $table->dropForeign(['program_choice_1_id']);
                $table->dropForeign(['program_choice_2_id']);
                $table->dropForeign(['accepted_program_id']);
            });
        }

        if (Schema::hasTable('classes')) {
            Schema::table('classes', function (Blueprint $table): void {
                $table->dropForeign(['department_id']);
            });
        }

        Schema::dropIfExists('departments');
    }
};
