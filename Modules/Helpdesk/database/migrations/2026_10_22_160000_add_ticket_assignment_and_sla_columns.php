<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table): void {
            if (! Schema::hasColumn('ticket_categories', 'response_hours')) {
                $table->unsignedInteger('response_hours')->default(4);
            }

            if (! Schema::hasColumn('ticket_categories', 'resolution_hours')) {
                $table->unsignedInteger('resolution_hours')->default(24);
            }

            if (! Schema::hasColumn('ticket_categories', 'default_assignee_user_id')) {
                $table->foreignId('default_assignee_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('tickets', function (Blueprint $table): void {
            if (! Schema::hasColumn('tickets', 'ticket_category_id')) {
                $table->foreignId('ticket_category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
            }

            if (! Schema::hasColumn('tickets', 'priority')) {
                $table->string('priority')->default('normal');
            }

            if (! Schema::hasColumn('tickets', 'assigned_to_user_id')) {
                $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('tickets', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable();
            }

            if (! Schema::hasColumn('tickets', 'closed_at')) {
                $table->timestamp('closed_at')->nullable();
            }

            if (! Schema::hasColumn('tickets', 'ai_suggested_category')) {
                $table->string('ai_suggested_category')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Forward-only additive repair: never drop ticket history or columns that
        // may have existed before this migration in a partially migrated system.
    }
};
