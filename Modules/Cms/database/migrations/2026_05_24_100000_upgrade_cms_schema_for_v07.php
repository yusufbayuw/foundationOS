<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sites')) {
            return;
        }

        Schema::table('sites', function (Blueprint $table): void {
            if (! Schema::hasColumn('sites', 'domain')) {
                $table->string('domain')->nullable()->after('name');
            }
            if (! Schema::hasColumn('sites', 'default_locale')) {
                $table->string('default_locale', 5)->default('id')->after('domain');
            }
            if (! Schema::hasColumn('sites', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('default_locale');
            }
        });

        if (Schema::hasTable('pages') && ! Schema::hasColumn('pages', 'slug')) {
            Schema::table('pages', function (Blueprint $table): void {
                $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
                $table->string('slug')->nullable();
                $table->string('title_id')->nullable();
                $table->string('title_en')->nullable();
                $table->string('template')->default('default');
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('og_image')->nullable();
                $table->timestamp('publish_at')->nullable();
                $table->timestamp('published_at')->nullable();
            });
        }

        if (Schema::hasTable('page_blocks') && ! Schema::hasColumn('page_blocks', 'block_type')) {
            Schema::table('page_blocks', function (Blueprint $table): void {
                $table->foreignId('page_id')->nullable()->constrained('pages')->cascadeOnDelete();
                $table->string('block_type')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->json('content')->nullable();
            });
        }
    }

    public function down(): void
    {
        //
    }
};
