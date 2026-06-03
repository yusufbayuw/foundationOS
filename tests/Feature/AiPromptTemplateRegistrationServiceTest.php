<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Ai\Exceptions\DuplicateAiPromptTemplateCodeException;
use Modules\Ai\Models\AiPromptTemplate;
use Modules\Ai\Services\AiPromptTemplateRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class AiPromptTemplateRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_template_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'ai']);

        $template = app(AiPromptTemplateRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' wf_sum ',
            name: 'Workflow summary',
            meta: ['feature' => 'workflow_summary'],
        );

        $this->assertSame('WF_SUM', $template->code);
        $this->assertSame('workflow_summary', $template->meta['feature']);
        $this->assertDatabaseHas(AiPromptTemplate::class, ['id' => $template->id, 'code' => 'WF_SUM']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'ai']);

        $service = app(AiPromptTemplateRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'HELP', 'Help draft');

        $this->expectException(DuplicateAiPromptTemplateCodeException::class);
        $service->register($tenant->id, $organization->id, 'help', 'Duplicate');
    }
}
