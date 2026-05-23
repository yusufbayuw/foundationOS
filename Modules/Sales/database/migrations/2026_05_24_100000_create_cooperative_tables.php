<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table): void {
                if (! Schema::hasColumn('customers', 'is_cooperative_member')) {
                    $table->boolean('is_cooperative_member')->default(false)->after('is_active');
                }
                if (! Schema::hasColumn('customers', 'member_number')) {
                    $table->string('member_number')->nullable()->after('is_cooperative_member');
                }
                if (! Schema::hasColumn('customers', 'member_discount_percent')) {
                    $table->decimal('member_discount_percent', 5, 2)->default(0)->after('member_number');
                }
            });
        }

        Schema::create('cooperative_savings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('savings_type');
            $table->decimal('amount', 18, 2);
            $table->date('transaction_date');
            $table->string('status')->default('posted');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('cooperative_loans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->decimal('principal_amount', 18, 2);
            $table->decimal('outstanding_amount', 18, 2);
            $table->unsignedSmallInteger('term_months');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('loan_installments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('cooperative_loan_id')->constrained('cooperative_loans')->cascadeOnDelete();
            $table->unsignedSmallInteger('installment_number');
            $table->date('due_date');
            $table->decimal('amount', 18, 2);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->unique(['cooperative_loan_id', 'installment_number']);
        });

        Schema::create('shu_distributions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year');
            $table->decimal('total_shu', 18, 2);
            $table->string('status')->default('draft');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();
            $table->unique(['tenant_id', 'fiscal_year']);
        });

        Schema::create('shu_distribution_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shu_distribution_id')->constrained('shu_distributions')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->decimal('amount', 18, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_distribution_lines');
        Schema::dropIfExists('shu_distributions');
        Schema::dropIfExists('loan_installments');
        Schema::dropIfExists('cooperative_loans');
        Schema::dropIfExists('cooperative_savings');
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table): void {
                foreach (['member_discount_percent', 'member_number', 'is_cooperative_member'] as $col) {
                    if (Schema::hasColumn('customers', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
