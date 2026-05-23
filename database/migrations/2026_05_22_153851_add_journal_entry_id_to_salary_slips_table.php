<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table): void {
            $table->foreignId('journal_entry_id')
                ->nullable()
                ->after('notes')
                ->constrained('journal_entries')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('journal_entry_id');
        });
    }
};
