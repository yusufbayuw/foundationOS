<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->string('membership_proof')->nullable()->after('member_type_id');
            $table->json('profile_data')->nullable()->after('membership_proof');
            $table->timestamp('verified_at')->nullable()->after('profile_data');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn([
                'membership_proof',
                'profile_data',
                'verified_at',
                'rejection_reason',
            ]);
        });
    }
};
