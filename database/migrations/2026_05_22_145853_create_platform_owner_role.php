<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // platform_owner is a global role (no team/tenant scope) for cross-tenant SaaS admins
        Role::firstOrCreate(
            ['name' => 'platform_owner', 'guard_name' => 'web'],
        );
    }

    public function down(): void
    {
        Role::where('name', 'platform_owner')->where('guard_name', 'web')->delete();
    }
};
