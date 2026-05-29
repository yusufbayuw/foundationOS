<?php

namespace Modules\Exam\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;
use Modules\Exam\Services\ExamShieldProvisioner;

class ProvisionExamSecurityCommand extends Command
{
    protected $signature = 'exam:provision-security {tenantId? : Tenant ID to provision roles for}';

    protected $description = 'Provision Exam module Shield roles and custom permissions for one or all tenants';

    public function handle(ExamShieldProvisioner $provisioner): int
    {
        $tenantId = $this->argument('tenantId');

        if ($tenantId !== null) {
            $tenant = Tenant::query()->find($tenantId);

            if ($tenant === null) {
                $this->error('Tenant not found.');

                return self::FAILURE;
            }

            $provisioner->provisionForTenant($tenant);
            $this->info("Exam security provisioned for tenant {$tenant->id}.");

            return self::SUCCESS;
        }

        Tenant::query()->each(function (Tenant $tenant) use ($provisioner): void {
            $provisioner->provisionForTenant($tenant);
            $this->line("Provisioned tenant {$tenant->id} ({$tenant->code}).");
        });

        $this->info('Exam security provisioned for all tenants.');

        return self::SUCCESS;
    }
}
