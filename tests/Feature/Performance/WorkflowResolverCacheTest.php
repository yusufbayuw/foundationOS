<?php

namespace Tests\Feature\Performance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Contracts\WorkflowResolver;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class WorkflowResolverCacheTest extends TestCase
{
    use CreatesTenantForTests;
    use RefreshDatabase;

    public function test_workflow_resolver_uses_cache_for_repeated_lookups(): void
    {
        $context = $this->makeTenantContext(['core', 'procurement', 'workflow']);

        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $context['tenant']->id,
            '--manager' => $context['user']->id,
            '--finance' => $context['user']->id,
            '--executive' => $context['user']->id,
        ])->assertSuccessful();

        $requisition = PurchaseRequisition::create([
            'tenant_id' => $context['tenant']->id,
            'user_id' => $context['user']->id,
            'requested_by' => $context['user']->id,
            'request_number' => 'PR-CACHE-'.Str::upper(Str::random(4)),
            'request_date' => now()->toDateString(),
            'status' => 'draft',
            'total_estimated_amount' => 1_000_000,
        ]);

        Cache::flush();

        $resolver = app(WorkflowResolver::class);

        $firstQueryCount = $this->countQueries(function () use ($resolver, $requisition, $context): void {
            $resolver->resolveForSubject(
                $requisition->workflowSubjectType(),
                $requisition,
                (int) $context['tenant']->id,
                null,
            );
        });

        $secondQueryCount = $this->countQueries(function () use ($resolver, $requisition, $context): void {
            $resolver->resolveForSubject(
                $requisition->workflowSubjectType(),
                $requisition,
                (int) $context['tenant']->id,
                null,
            );
        });

        $this->assertGreaterThan(0, $firstQueryCount);
        $this->assertSame(1, $secondQueryCount);
    }

    private function countQueries(callable $callback): int
    {
        $count = 0;

        DB::listen(function () use (&$count): void {
            $count++;
        });

        $callback();

        return $count;
    }
}
