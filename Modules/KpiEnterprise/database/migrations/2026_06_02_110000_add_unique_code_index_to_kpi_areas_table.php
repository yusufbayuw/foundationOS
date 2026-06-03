<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_areas', function (Blueprint $table) {
            $table->unique(
                ['tenant_id', 'organization_id', 'code'],
                'kpi_areas_tenant_org_code_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('kpi_areas', function (Blueprint $table) {
            $table->dropUnique('kpi_areas_tenant_org_code_unique');
        });
    }
};
