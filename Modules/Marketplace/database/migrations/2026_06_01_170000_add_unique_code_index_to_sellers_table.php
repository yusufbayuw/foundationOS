<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->unique(
                ['tenant_id', 'organization_id', 'code'],
                'sellers_tenant_org_code_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropUnique('sellers_tenant_org_code_unique');
        });
    }
};
