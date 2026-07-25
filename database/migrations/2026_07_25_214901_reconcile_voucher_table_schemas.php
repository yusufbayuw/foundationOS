<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table): void {
            if (! Schema::hasColumn('vouchers', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            }

            if (! Schema::hasColumn('vouchers', 'owner_type')) {
                $table->string('owner_type')->nullable();
            }

            if (! Schema::hasColumn('vouchers', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable();
            }

            if (! Schema::hasColumn('vouchers', 'title')) {
                $table->string('title')->nullable();
            }

            if (! Schema::hasColumn('vouchers', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('vouchers', 'quota')) {
                $table->unsignedInteger('quota')->default(1);
            }

            if (! Schema::hasColumn('vouchers', 'claimed_count')) {
                $table->unsignedInteger('claimed_count')->default(0);
            }

            if (! Schema::hasColumn('vouchers', 'start_at')) {
                $table->timestamp('start_at')->nullable();
            }

            if (! Schema::hasColumn('vouchers', 'end_at')) {
                $table->timestamp('end_at')->nullable();
            }

            if (! Schema::hasColumn('vouchers', 'terms')) {
                $table->text('terms')->nullable();
            }

            if (! Schema::hasColumn('vouchers', 'redemption_method')) {
                $table->string('redemption_method')->default('manual');
            }

            if (! Schema::hasColumn('vouchers', 'meta')) {
                $table->json('meta')->nullable();
            }
        });

        Schema::table('voucher_claims', function (Blueprint $table): void {
            if (! Schema::hasColumn('voucher_claims', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            }

            if (! Schema::hasColumn('voucher_claims', 'claim_code')) {
                $table->string('claim_code')->nullable();
            }

            if (! Schema::hasColumn('voucher_claims', 'claimed_at')) {
                $table->timestamp('claimed_at')->nullable();
            }

            if (! Schema::hasColumn('voucher_claims', 'used_at')) {
                $table->timestamp('used_at')->nullable();
            }

            if (! Schema::hasColumn('voucher_claims', 'redeemed_at')) {
                $table->timestamp('redeemed_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Existing voucher data may rely on the reconciled columns.
    }
};
