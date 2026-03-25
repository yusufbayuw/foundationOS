<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;

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
                'trial_ends_at' => now()->addDays(30),
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

        $tenantRole = TenantRole::query()->updateOrCreate(
            [
                'tenant_id' => $tenant->getKey(),
                'slug' => 'owner',
            ],
            [
                'name' => 'Owner',
                'description' => 'Pemilik tenant dan administrator utama MVP.',
                'level' => 100,
                'permissions' => ['*'],
                'is_default' => true,
                'is_super_admin' => true,
                'dashboard_route' => 'filament.admin.pages.dashboard',
            ],
        );

        UserTenantRole::query()->updateOrCreate(
            [
                'user_id' => $admin->getKey(),
                'tenant_id' => $tenant->getKey(),
                'tenant_role_id' => $tenantRole->getKey(),
            ],
            [
                'organization_id' => $organization->getKey(),
                'assigned_by' => $admin->getKey(),
                'assigned_at' => now(),
                'is_primary' => true,
            ],
        );
    }
}
