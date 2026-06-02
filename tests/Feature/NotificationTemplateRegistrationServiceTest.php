<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Messaging\Exceptions\DuplicateNotificationTemplateCodeException;
use Modules\Messaging\Models\NotificationTemplate;
use Modules\Messaging\Services\NotificationTemplateRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class NotificationTemplateRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_template_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'messaging']);

        $template = app(NotificationTemplateRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' welcome ',
            name: 'Welcome Email',
        );

        $this->assertSame('WELCOME', $template->code);
        $this->assertDatabaseHas(NotificationTemplate::class, ['id' => $template->id, 'code' => 'WELCOME']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'messaging']);

        $service = app(NotificationTemplateRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'ALERT', 'Alert SMS');

        $this->expectException(DuplicateNotificationTemplateCodeException::class);
        $service->register($tenant->id, $organization->id, 'alert', 'Duplicate');
    }
}
