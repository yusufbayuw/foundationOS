<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Event\Exceptions\DuplicateEventCodeException;
use Modules\Event\Models\Event;
use Modules\Event\Services\EventRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class EventRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_event_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'event']);

        $event = app(EventRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' grad26 ',
            name: 'Graduation 2026',
        );

        $this->assertSame('GRAD26', $event->code);
        $this->assertDatabaseHas(Event::class, ['id' => $event->id, 'code' => 'GRAD26']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'event']);

        $service = app(EventRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'OPEN', 'Open House');

        $this->expectException(DuplicateEventCodeException::class);
        $service->register($tenant->id, $organization->id, 'open', 'Duplicate');
    }
}
