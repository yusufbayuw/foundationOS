<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Tenant;
use Modules\Core\Services\ApplicationModuleCatalog;
use Modules\Core\Services\TenantModuleProvisioner;
use Modules\Helpdesk\Models\TicketCategory;
use Modules\Legal\Models\LegalDocument;

class RoadmapV05FacilitiesSeeder extends Seeder
{
    public function run(): void
    {
        app(ApplicationModuleCatalog::class)->sync();

        $tenant = Tenant::query()->where('code', 'FOUNDATION-DEMO')->first();

        if (! $tenant) {
            return;
        }

        app(TenantModuleProvisioner::class)->enableForTenant($tenant, [
            'legal', 'asset', 'dms', 'helpdesk', 'facility', 'eoffice', 'itops',
            'transport', 'boarding', 'cafeteria', 'physicalsecurity',
        ]);

        TicketCategory::query()->updateOrCreate(
            ['tenant_id' => $tenant->getKey(), 'code' => 'IT'],
            [
                'name' => 'IT Support',
                'status' => 'active',
                'response_hours' => 2,
                'resolution_hours' => 8,
            ],
        );

        LegalDocument::query()->updateOrCreate(
            ['tenant_id' => $tenant->getKey(), 'code' => 'AKTA'],
            [
                'name' => 'Foundation deed',
                'document_type' => 'akta',
                'status' => 'active',
            ],
        );
    }
}
