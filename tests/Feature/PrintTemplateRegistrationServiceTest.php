<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Printing\Exceptions\DuplicatePrintTemplateCodeException;
use Modules\Printing\Models\PrintTemplate;
use Modules\Printing\Services\PrintTemplateRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class PrintTemplateRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_template_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'printing']);

        $template = app(PrintTemplateRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' cert ',
            name: 'Certificate A4',
        );

        $this->assertSame('CERT', $template->code);
        $this->assertDatabaseHas(PrintTemplate::class, ['id' => $template->id, 'code' => 'CERT']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'printing']);

        $service = app(PrintTemplateRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'ID', 'Student ID Card');

        $this->expectException(DuplicatePrintTemplateCodeException::class);
        $service->register($tenant->id, $organization->id, 'id', 'Duplicate');
    }
}
