<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;
use Modules\Core\Services\ApplicationModuleCatalog;
use Modules\Core\Services\TenantAdminProvisioner;
use Modules\Core\Services\TenantModuleProvisioner;

class ProvisionTenantModulesCommand extends Command
{
    protected $signature = 'foundation:provision-tenant-modules
        {tenant : Tenant ID, UUID, or code}
        {--modules= : Comma-separated module codes (default: all active modules)}
        {--with-shield : Assign Filament Shield super_admin role to tenant owners}';

    protected $description = 'Sync module catalog and enable tenant modules for navigation';

    public function handle(
        ApplicationModuleCatalog $catalog,
        TenantModuleProvisioner $moduleProvisioner,
        TenantAdminProvisioner $adminProvisioner,
    ): int {
        $tenant = $this->resolveTenant((string) $this->argument('tenant'));

        if (! $tenant) {
            $this->error('Tenant not found.');

            return self::FAILURE;
        }

        $catalog->sync();

        $moduleCodes = $this->option('modules')
            ? array_map('trim', explode(',', (string) $this->option('modules')))
            : null;

        $moduleProvisioner->enableForTenant($tenant, $moduleCodes);

        if ($this->option('with-shield')) {
            foreach ($tenant->userTenantRoles()->with('user')->get() as $assignment) {
                if ($assignment->user) {
                    $adminProvisioner->assignShieldSuperAdmin($assignment->user, $tenant);
                }
            }
        }

        $enabledCount = $tenant->tenantModules()->where('is_enabled', true)->count();

        $this->info("Tenant [{$tenant->code}] now has {$enabledCount} enabled module(s).");
        $this->line("Admin URL: /admin/{$tenant->uuid}");

        return self::SUCCESS;
    }

    protected function resolveTenant(string $identifier): ?Tenant
    {
        return Tenant::query()
            ->when(
                is_numeric($identifier),
                fn ($query) => $query->where('id', (int) $identifier),
                fn ($query) => $query->where(function ($query) use ($identifier): void {
                    $query->where('uuid', $identifier)->orWhere('code', $identifier);
                }),
            )
            ->first();
    }
}
