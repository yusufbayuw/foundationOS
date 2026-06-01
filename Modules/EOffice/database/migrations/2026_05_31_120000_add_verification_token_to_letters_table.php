<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('letters')) {
            return;
        }

        Schema::table('letters', function (Blueprint $table) {
            if (! Schema::hasColumn('letters', 'verification_token')) {
                $table->string('verification_token')->nullable()->unique()->after('letter_number');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('letters')) {
            return;
        }

        Schema::table('letters', function (Blueprint $table) {
            if (Schema::hasColumn('letters', 'verification_token')) {
                $table->dropColumn('verification_token');
            }
        });
    }
};
