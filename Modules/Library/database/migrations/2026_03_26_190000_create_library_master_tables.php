<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_publishers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('room')->nullable();
            $table->string('shelf')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_item_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->boolean('is_loanable')->default(true);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_collection_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->boolean('is_reference_only')->default(false);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_gmds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_frequencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->unsignedInteger('interval_days')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_member_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->unsignedInteger('membership_period_days')->default(365);
            $table->unsignedInteger('max_books')->default(3);
            $table->unsignedInteger('loan_period_days')->default(7);
            $table->decimal('fine_per_day', 10, 2)->default(1000);
            $table->unsignedInteger('max_extensions')->default(2);
            $table->unsignedInteger('grace_period_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('frequency_id')->nullable()->constrained('library_frequencies')->nullOnDelete();
            $table->string('title');
            $table->string('issn')->nullable();
            $table->date('start_date')->nullable();
            $table->string('period')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_serial_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained('library_serials')->cascadeOnDelete();
            $table->date('expected_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('sequence_number')->nullable();
            $table->string('status')->default('expected');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_stock_takes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_stock_take_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_take_id')->constrained('library_stock_takes')->cascadeOnDelete();
            $table->foreignId('book_copy_id')->nullable()->constrained('book_copies')->nullOnDelete();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scanned_at')->nullable();
            $table->string('status')->default('found');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('book_author', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('library_authors')->cascadeOnDelete();
            $table->unique(['book_id', 'author_id']);
        });

        Schema::create('book_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('library_subjects')->cascadeOnDelete();
            $table->unique(['book_id', 'subject_id']);
        });

        Schema::table('books', function (Blueprint $table): void {
            $table->foreignId('publisher_id')->nullable()->constrained('library_publishers')->nullOnDelete();
            $table->foreignId('gmd_id')->nullable()->constrained('library_gmds')->nullOnDelete();
            $table->foreignId('collection_type_id')->nullable()->constrained('library_collection_types')->nullOnDelete();
            $table->foreignId('frequency_id')->nullable()->constrained('library_frequencies')->nullOnDelete();
        });

        Schema::table('book_copies', function (Blueprint $table): void {
            $table->foreignId('location_id')->nullable()->constrained('library_locations')->nullOnDelete();
            $table->foreignId('item_status_id')->nullable()->constrained('library_item_statuses')->nullOnDelete();
            $table->foreignId('collection_type_id')->nullable()->constrained('library_collection_types')->nullOnDelete();
        });

        Schema::table('members', function (Blueprint $table): void {
            $table->foreignId('member_type_id')->nullable()->constrained('library_member_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('member_type_id');
        });

        Schema::table('book_copies', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('location_id');
            $table->dropConstrainedForeignId('item_status_id');
            $table->dropConstrainedForeignId('collection_type_id');
        });

        Schema::table('books', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('publisher_id');
            $table->dropConstrainedForeignId('gmd_id');
            $table->dropConstrainedForeignId('collection_type_id');
            $table->dropConstrainedForeignId('frequency_id');
        });

        Schema::dropIfExists('book_subject');
        Schema::dropIfExists('book_author');
        Schema::dropIfExists('library_stock_take_items');
        Schema::dropIfExists('library_stock_takes');
        Schema::dropIfExists('library_serial_issues');
        Schema::dropIfExists('library_serials');
        Schema::dropIfExists('library_member_types');
        Schema::dropIfExists('library_frequencies');
        Schema::dropIfExists('library_gmds');
        Schema::dropIfExists('library_collection_types');
        Schema::dropIfExists('library_item_statuses');
        Schema::dropIfExists('library_locations');
        Schema::dropIfExists('library_subjects');
        Schema::dropIfExists('library_publishers');
        Schema::dropIfExists('library_authors');
    }
};
