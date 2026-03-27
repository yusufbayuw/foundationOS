<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table): void {
            $table->boolean('ready_for_sourcing')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table): void {
            $table->dropColumn('ready_for_sourcing');
        });
    }
};
