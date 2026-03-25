<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reversed_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->string('entry_number');
            $table->date('date');
            $table->text('description');
            $table->decimal('total_debit', 12, 2);
            $table->decimal('total_credit', 12, 2);
            $table->boolean('is_balanced')->default(true);
            $table->boolean('is_posted')->default(false);
            $table->timestamp('posted_at')->nullable();
            $table->boolean('is_reversed')->default(false);
            $table->text('reversal_reason')->nullable();
            $table->unique(['tenant_id', 'entry_number']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
