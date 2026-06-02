<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Helpdesk\Exceptions\DuplicateTicketCategoryCodeException;
use Modules\Helpdesk\Models\TicketCategory;
use Modules\Helpdesk\Services\TicketCategoryRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class TicketCategoryRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_category_with_sla_hours(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'helpdesk']);

        $category = app(TicketCategoryRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' net ',
            name: 'Network Issues',
            responseHours: 2,
            resolutionHours: 8,
            description: 'Connectivity problems',
        );

        $this->assertSame('NET', $category->code);
        $this->assertSame(2, $category->response_hours);
        $this->assertSame(8, $category->resolution_hours);
        $this->assertDatabaseHas(TicketCategory::class, [
            'id' => $category->id,
            'tenant_id' => $tenant->id,
            'code' => 'NET',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'helpdesk']);

        $service = app(TicketCategoryRegistrationService::class);

        $service->register($tenant->id, $organization->id, 'HR', 'HR Support');

        $this->expectException(DuplicateTicketCategoryCodeException::class);

        $service->register($tenant->id, $organization->id, 'hr', 'HR duplicate');
    }
}
