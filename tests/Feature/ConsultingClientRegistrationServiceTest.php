<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Consulting\Exceptions\DuplicateConsultingClientCodeException;
use Modules\Consulting\Models\ConsultingClient;
use Modules\Consulting\Services\ConsultingClientRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class ConsultingClientRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_client_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'consulting']);

        $client = app(ConsultingClientRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' acme ',
            name: 'Acme Corporation',
        );

        $this->assertSame('ACME', $client->code);
        $this->assertDatabaseHas(ConsultingClient::class, ['id' => $client->id, 'code' => 'ACME']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'consulting']);

        $service = app(ConsultingClientRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'BETA', 'Beta Ltd');

        $this->expectException(DuplicateConsultingClientCodeException::class);
        $service->register($tenant->id, $organization->id, 'beta', 'Duplicate');
    }
}
