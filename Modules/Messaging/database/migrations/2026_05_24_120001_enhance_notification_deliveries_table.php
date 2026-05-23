<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_deliveries', function (Blueprint $table) {
            $table->string('idempotency_key')->nullable()->unique()->after('meta');
        });

        Schema::table('notification_deliveries', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->change();
        });

        Schema::table('notification_templates', function (Blueprint $table) {
            $table->string('channel')->default('database')->after('name');
            $table->string('category')->nullable()->after('channel');
            $table->unsignedInteger('version')->default(1)->after('category');
            $table->text('body_template')->nullable()->after('description');
        });

        Schema::table('notification_preferences', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->string('category')->nullable()->after('user_id');
            $table->boolean('database_enabled')->default(true);
            $table->boolean('mail_enabled')->default(true);
            $table->boolean('whatsapp_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('notification_deliveries', function (Blueprint $table) {
            $table->dropColumn('idempotency_key');
        });

        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropColumn(['channel', 'category', 'version', 'body_template']);
        });

        Schema::table('notification_preferences', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['category', 'database_enabled', 'mail_enabled', 'whatsapp_enabled']);
        });
    }
};
