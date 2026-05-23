<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // platform_owner is a global SaaS role; assignments use tenant_id 0 (see User::canAccessPanel).
        Role::firstOrCreate(
            ['name' => 'platform_owner', 'guard_name' => 'web'],
        );
    }

    public function down(): void
    {
        Role::where('name', 'platform_owner')->where('guard_name', 'web')->delete();
    }
};
