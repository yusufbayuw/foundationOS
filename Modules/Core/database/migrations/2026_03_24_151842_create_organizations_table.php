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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('type')->nullable();
            $table->string('level')->nullable();
            $table->string('npsn')->nullable();
            $table->string('nss')->nullable();
            $table->string('accreditation_status')->nullable();
            $table->string('npwp')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('province_id')->nullable()->index();
            $table->unsignedBigInteger('city_id')->nullable()->index();
            $table->unsignedBigInteger('district_id')->nullable()->index();
            $table->unsignedBigInteger('village_id')->nullable()->index();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->date('established_date')->nullable();
            $table->unsignedBigInteger('principal_user_id')->nullable()->index();
            $table->string('logo')->nullable();
            $table->string('stamp')->nullable();
            $table->string('signature')->nullable();
            $table->string('letterhead')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->unique(['tenant_id', 'code']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
