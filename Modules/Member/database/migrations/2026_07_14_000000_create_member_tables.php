<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->timestamps();
        });

        if (! Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('member_type_id')->constrained('member_types')->restrictOnDelete();
                $table->string('status', 20)->default('pending')->index();
                $table->timestamp('verified_at')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'member_type_id']);
            });
        } else {
            Schema::table('members', function (Blueprint $table): void {
                if (! Schema::hasColumn('members', 'domain_member_type_id')) {
                    $table->foreignId('domain_member_type_id')->nullable()->after('user_id')->constrained('member_types')->restrictOnDelete();
                }

                if (! Schema::hasColumn('members', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('status');
                }

                if (! Schema::hasColumn('members', 'verified_by')) {
                    $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('members', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('verified_by');
                }
            });
        }

        Schema::create('member_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->unique()->constrained('members')->cascadeOnDelete();
            $table->json('profile_data');
            $table->timestamps();
        });

        Schema::create('member_proofs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('disk')->default('local');
            $table->string('mime_type', 100);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });

        DB::table('member_types')->insert([
            ['code' => 'alumni', 'name' => 'Alumni', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'teacher', 'name' => 'Teacher', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'lecturer', 'name' => 'Lecturer', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'student', 'name' => 'Student', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'college_student', 'name' => 'College Student', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'employee', 'name' => 'Employee', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('member_proofs');
        Schema::dropIfExists('member_profiles');

        if (Schema::hasTable('members') && Schema::hasColumn('members', 'tenant_id') && Schema::hasColumn('members', 'domain_member_type_id')) {
            Schema::table('members', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('domain_member_type_id');
                $table->dropConstrainedForeignId('verified_by');
                $table->dropColumn(['verified_at', 'rejection_reason']);
            });
        } else {
            Schema::dropIfExists('members');
        }

        Schema::dropIfExists('member_types');
    }
};
