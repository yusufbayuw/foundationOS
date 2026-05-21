<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workflow_steps', function (Blueprint $table): void {
            $table->string('gateway_type', 32)->default('none')->after('step_type');
            $table->string('quorum_strategy', 32)->nullable()->after('gateway_type');
            $table->unsignedInteger('quorum_value')->nullable()->after('quorum_strategy');
        });
    }

    public function down(): void
    {
        Schema::table('workflow_steps', function (Blueprint $table): void {
            $table->dropColumn(['gateway_type', 'quorum_strategy', 'quorum_value']);
        });
    }
};
