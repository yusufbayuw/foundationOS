<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Dms\Exceptions\DuplicateDocumentFolderCodeException;
use Modules\Dms\Models\DocumentFolder;
use Modules\Dms\Services\DocumentFolderRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class DocumentFolderRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_folder_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'dms']);

        $folder = app(DocumentFolderRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' hr ',
            name: 'Human Resources',
            description: 'HR documents',
            meta: ['retention_years' => 7],
        );

        $this->assertSame('HR', $folder->code);
        $this->assertSame('active', $folder->status);
        $this->assertSame(7, $folder->meta['retention_years']);
        $this->assertDatabaseHas(DocumentFolder::class, [
            'id' => $folder->id,
            'tenant_id' => $tenant->id,
            'code' => 'HR',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'dms']);

        $service = app(DocumentFolderRegistrationService::class);

        $service->register($tenant->id, $organization->id, 'FIN', 'Finance');

        $this->expectException(DuplicateDocumentFolderCodeException::class);

        $service->register($tenant->id, $organization->id, 'fin', 'Finance duplicate');
    }
}
