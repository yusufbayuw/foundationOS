<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('allergies', function (Blueprint $table) {
            $table->unique(
                ['tenant_id', 'organization_id', 'code'],
                'allergies_tenant_org_code_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('allergies', function (Blueprint $table) {
            $table->dropUnique('allergies_tenant_org_code_unique');
        });
    }
};
