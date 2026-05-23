<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds required_evidence JSON column to workflow_steps.
 *
 * Schema: { "file_count": int, "types": ["image/jpeg","application/pdf"], "label": string }
 * file_count = 0 means no evidence required (default/null).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workflow_steps', function (Blueprint $table): void {
            $table->json('required_evidence')->nullable()->after('action_schema');
        });
    }

    public function down(): void
    {
        Schema::table('workflow_steps', function (Blueprint $table): void {
            $table->dropColumn('required_evidence');
        });
    }
};
