<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\EOffice\Exceptions\DuplicateLetterCategoryCodeException;
use Modules\EOffice\Models\LetterCategory;
use Modules\EOffice\Services\LetterCategoryRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class LetterCategoryRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_category_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'eoffice']);

        $category = app(LetterCategoryRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' in ',
            name: 'Incoming Letters',
            description: 'External correspondence received',
        );

        $this->assertSame('IN', $category->code);
        $this->assertSame('active', $category->status);
        $this->assertDatabaseHas(LetterCategory::class, [
            'id' => $category->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'IN',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'eoffice']);

        $service = app(LetterCategoryRegistrationService::class);

        $service->register($tenant->id, $organization->id, 'OUT', 'Outgoing');

        $this->expectException(DuplicateLetterCategoryCodeException::class);

        $service->register($tenant->id, $organization->id, 'out', 'Outgoing duplicate');
    }
}
