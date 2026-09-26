<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            ! Schema::hasColumn('instructors', 'organization_id')
            || ! Schema::hasColumn('instructors', 'code')
            || Schema::hasIndex('instructors', 'instructors_tenant_org_code_unique')
        ) {
            return;
        }

        Schema::table('instructors', function (Blueprint $table): void {
            $table->unique(
                ['tenant_id', 'organization_id', 'code'],
                'instructors_tenant_org_code_unique',
            );
        });
    }

    public function down(): void
    {
        if (! Schema::hasIndex('instructors', 'instructors_tenant_org_code_unique')) {
            return;
        }

        Schema::table('instructors', function (Blueprint $table): void {
            $table->dropUnique('instructors_tenant_org_code_unique');
        });
    }
};
