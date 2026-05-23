<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->date('effective_date')->nullable()->after('description');
            $table->date('expires_at')->nullable()->after('effective_date');
            $table->boolean('auto_renew')->default(false)->after('expires_at');
            $table->unsignedInteger('notice_period_days')->default(30)->after('auto_renew');
            $table->foreignId('owner_unit_id')->nullable()->after('notice_period_days')->constrained('organizations')->nullOnDelete();
        });

        Schema::table('letters', function (Blueprint $table) {
            $table->string('verification_token')->nullable()->unique()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_unit_id');
            $table->dropColumn(['effective_date', 'expires_at', 'auto_renew', 'notice_period_days']);
        });

        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn('verification_token');
        });
    }
};
