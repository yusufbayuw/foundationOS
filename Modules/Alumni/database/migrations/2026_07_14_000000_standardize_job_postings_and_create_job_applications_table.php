<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('company')->nullable()->after('description');
            $table->string('role_title')->nullable()->after('company');
            $table->string('location')->nullable()->after('role_title');
            $table->string('employment_type')->nullable()->after('location');
            $table->string('application_method')->default('external_url')->after('employment_type');
            $table->string('application_url')->nullable()->after('application_method');
            $table->string('application_email')->nullable()->after('application_url');
            $table->timestamp('expires_at')->nullable()->after('application_email');

            $table->index('expires_at', 'job_postings_expires_at_index');
            $table->index(['status', 'expires_at'], 'job_postings_status_expires_at_index');
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('submitted');
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'job_posting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex('job_postings_status_expires_at_index');
            $table->dropIndex('job_postings_expires_at_index');
            $table->dropColumn([
                'company',
                'role_title',
                'location',
                'employment_type',
                'application_method',
                'application_url',
                'application_email',
                'expires_at',
            ]);
        });
    }
};
