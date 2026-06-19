<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table): void {
            if (! Schema::hasColumn('instructors', 'organization_id')) {
                $table->foreignId('organization_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('organizations')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('instructors', 'code')) {
                $table->string('code')->nullable()->after('organization_id');
            }

            if (! Schema::hasColumn('instructors', 'status')) {
                $table->string('status')->default('active')->after('name');
            }

            if (! Schema::hasColumn('instructors', 'description')) {
                $table->text('description')->nullable()->after('status');
            }

            if (! Schema::hasColumn('instructors', 'meta')) {
                $table->json('meta')->nullable()->after('description');
            }

            if (! Schema::hasColumn('instructors', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table): void {
            if (Schema::hasColumn('instructors', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            foreach (['meta', 'description', 'status', 'code'] as $column) {
                if (Schema::hasColumn('instructors', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('instructors', 'organization_id')) {
                $table->dropConstrainedForeignId('organization_id');
            }
        });
    }
};
