<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Core\Services\TenantAdminProvisioner;

class MvpDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'is_super_admin' => true,
            ],
        );

        $tenant = Tenant::query()->updateOrCreate(
            ['code' => 'FOUNDATION-DEMO'],
            [
                'uuid' => '8a89d364-7f0d-4c6d-8f79-bd72b431ef01',
                'name' => 'Foundation Demo',
                'subdomain' => 'foundation-demo',
                'timezone' => 'Asia/Jakarta',
                'currency' => 'IDR',
                'locale' => 'id',
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'trial_ends_at' => now()->addYear(),
                'max_users' => 100,
                'max_organizations' => 5,
                'max_storage_mb' => 10240,
                'created_by' => $admin->getKey(),
                'settings' => [
                    'app_name' => 'Foundation Demo',
                ],
            ],
        );

        $organization = Organization::query()->updateOrCreate(
            [
                'tenant_id' => $tenant->getKey(),
                'code' => 'MAIN',
            ],
            [
                'name' => 'Foundation Demo Main Organization',
                'short_name' => 'Foundation Demo',
                'type' => 'school',
                'email' => 'admin@admin.com',
                'phone' => '081234567890',
                'principal_user_id' => $admin->getKey(),
                'is_main' => true,
                'is_active' => true,
            ],
        );

        app(TenantAdminProvisioner::class)->provisionDemoAdmin(
            $admin,
            $tenant,
            $organization->getKey(),
        );
    }
}
