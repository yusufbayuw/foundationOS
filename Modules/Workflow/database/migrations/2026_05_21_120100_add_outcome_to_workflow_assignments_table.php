<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workflow_assignments', function (Blueprint $table): void {
            $table->string('outcome', 64)->nullable()->after('status');
            $table->index(['workflow_instance_id', 'step_id', 'outcome'], 'wf_assign_instance_step_outcome_idx');
        });
    }

    public function down(): void
    {
        Schema::table('workflow_assignments', function (Blueprint $table): void {
            $table->dropIndex('wf_assign_instance_step_outcome_idx');
            $table->dropColumn('outcome');
        });
    }
};
