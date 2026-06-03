<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Ai\Services\AiAdvisorService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class AiAdvisorServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_advise_logs_call_and_returns_non_auto_executable_suggestion(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'ai']);

        $result = app(AiAdvisorService::class)->advise(
            tenantId: $tenant->id,
            feature: 'workflow_summary',
            input: ['subject' => 'Purchase order #42'],
        );

        $this->assertTrue($result['ai_suggestion']);
        $this->assertFalse($result['auto_executable']);
        $this->assertDatabaseHas('ai_call_logs', [
            'tenant_id' => $tenant->id,
            'feature' => 'workflow_summary',
        ]);
        $this->assertSame(1, DB::table('ai_call_logs')->where('tenant_id', $tenant->id)->count());
    }
}
