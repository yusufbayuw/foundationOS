<?php

namespace Tests\Feature;

use App\Services\ExecutiveWarningService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Ai\Services\AiAdvisorService;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\EducationQa\Services\SchoolHealthIndexService;
use Modules\InternalAudit\Models\AuditFinding;
use Modules\IsoCompliance\Services\ComplianceDashboardService;
use Modules\KpiEnterprise\Models\KpiActual;
use Modules\KpiEnterprise\Models\KpiMetric;
use Modules\KpiEnterprise\Models\KpiTarget;
use Modules\KpiEnterprise\Services\KpiRollupService;
use Modules\Monitoring\Models\AuditLog;
use Modules\Risk\Models\Risk;
use Tests\TestCase;

class RoadmapV08GrcIntelligenceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_high_audit_finding_creates_linked_risk(): void
    {
        [$tenant, $organization] = $this->makeTenantWithOrganization();

        $finding = AuditFinding::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'FND-HIGH-001',
            'name' => 'Backup evidence missing',
            'status' => 'open',
            'meta' => [
                'category' => 'IT',
                'severity' => 'high',
                'auditee_type' => Organization::class,
                'auditee_id' => $organization->id,
            ],
        ]);

        $risk = Risk::withoutTenantScope()->first();

        $this->assertNotNull($risk);
        $this->assertSame($finding->id, $risk->meta['source_audit_finding_id'] ?? null);
        $this->assertSame(Organization::class, $risk->riskable_type);
        $this->assertSame($organization->id, $risk->riskable_id);
        $this->assertSame(20, $risk->score);
    }

    public function test_audit_hash_chain_verifier_detects_tampering(): void
    {
        [$tenant] = $this->makeTenantWithOrganization();

        AuditLog::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'action' => 'security.login_failed',
            'category' => AuditLog::CATEGORY_SECURITY,
            'description' => 'Failed login',
        ]);

        AuditLog::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'action' => 'finance.journal_posted',
            'category' => 'finance',
            'description' => 'Journal posted',
        ]);

        $this->artisan('audit:verify-chain', ['--tenant' => $tenant->id])
            ->assertSuccessful()
            ->expectsOutputToContain('Audit hash chain verified');

        AuditLog::withoutTenantScope()->latest('id')->firstOrFail()->update([
            'description' => 'Tampered journal',
        ]);

        $this->artisan('audit:verify-chain', ['--tenant' => $tenant->id])
            ->assertExitCode(1)
            ->expectsOutputToContain('Audit hash chain mismatch');
    }

    public function test_kpi_rollup_pushes_parent_score_from_weighted_children(): void
    {
        [$tenant, $organization] = $this->makeTenantWithOrganization();

        $parent = KpiMetric::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'FIN',
            'name' => 'Financial Health',
        ]);

        $collectionRate = KpiMetric::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'COLLECTION',
            'name' => 'Collection Rate',
        ]);

        $runway = KpiMetric::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'RUNWAY',
            'name' => 'Cash Runway',
        ]);

        KpiTarget::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'kpi_metric_id' => $collectionRate->id,
            'period' => '2026-05',
            'target_value' => 100,
            'weight' => 60,
        ]);

        KpiTarget::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'kpi_metric_id' => $runway->id,
            'period' => '2026-05',
            'target_value' => 100,
            'weight' => 40,
        ]);

        KpiActual::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'kpi_metric_id' => $collectionRate->id,
            'period' => '2026-05',
            'actual_value' => 90,
        ]);

        KpiActual::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'kpi_metric_id' => $runway->id,
            'period' => '2026-05',
            'actual_value' => 70,
        ]);

        app(KpiRollupService::class)->linkChild($parent, $collectionRate);
        app(KpiRollupService::class)->linkChild($parent, $runway);

        $score = app(KpiRollupService::class)->rollup($parent, '2026-05');

        $this->assertEqualsWithDelta(82.0, $score, 0.01);
    }

    public function test_executive_warning_service_flags_runway_collection_and_utilization(): void
    {
        [$tenant] = $this->makeTenantWithOrganization();

        $warnings = app(ExecutiveWarningService::class)->scanTenant($tenant->id, [
            'cash_runway_months' => 2.5,
            'receivable_growth_percent' => 18,
            'room_utilization_percent' => 94,
            'teacher_workload_percent' => 80,
        ]);

        $this->assertContains('cashflow_runway_low', array_column($warnings, 'code'));
        $this->assertContains('receivables_spike', array_column($warnings, 'code'));
        $this->assertContains('room_overload', array_column($warnings, 'code'));
    }

    public function test_ai_advisor_uses_fake_provider_and_records_cost_without_auto_execute(): void
    {
        [$tenant] = $this->makeTenantWithOrganization();

        $response = app(AiAdvisorService::class)->advise($tenant->id, 'executive_qa', [
            'question' => 'Apa risiko utama bulan ini?',
        ]);

        $this->assertTrue($response['ai_suggestion']);
        $this->assertFalse($response['auto_executable']);
        $this->assertDatabaseHas('ai_call_logs', [
            'tenant_id' => $tenant->id,
            'feature' => 'executive_qa',
            'provider' => 'fake',
        ]);
    }

    public function test_compliance_dashboard_score_is_deterministic(): void
    {
        [$tenant, $organization] = $this->makeTenantWithOrganization();

        $score = app(ComplianceDashboardService::class)->score($tenant->id, $organization->id, [
            'A.5.1' => 'implemented',
            'A.5.2' => 'partial',
            'A.5.3' => 'not_implemented',
            'A.5.4' => 'audited',
        ]);

        $this->assertSame([
            'maturity_score' => 56.25,
            'control_coverage_percent' => 75.0,
            'audit_readiness_score' => 25.0,
        ], $score);
    }

    public function test_school_health_index_combines_multi_unit_signals(): void
    {
        [$tenant, $organization] = $this->makeTenantWithOrganization();

        $index = app(SchoolHealthIndexService::class)->compute($tenant->id, $organization->id, [
            'academic_score' => 80,
            'collection_rate' => 90,
            'runway_score' => 70,
            'hr_retention_score' => 75,
            'asset_utilization_score' => 65,
            'satisfaction_score' => 85,
        ]);

        $this->assertSame(78.0, $index['score']);
        $this->assertSame('healthy', $index['band']);
        $this->assertDatabaseHas('school_health_indices', [
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'period' => now()->format('Y-m'),
            'score' => 78.0,
            'band' => 'healthy',
        ]);
    }

    /**
     * @return array{0: Tenant, 1: Organization}
     */
    private function makeTenantWithOrganization(): array
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'enterprise',
            'name' => 'Enterprise',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'included_modules' => [],
            'features' => [],
            'is_active' => true,
        ]);

        $tenant = Tenant::query()->create([
            'uuid' => (string) str()->uuid(),
            'code' => 'T'.str()->upper(str()->random(6)),
            'name' => 'Yayasan Test',
            'status' => 'active',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'code' => 'ORG',
            'name' => 'Unit Test',
            'type' => 'school',
            'is_active' => true,
        ]);

        return [$tenant, $organization];
    }
}
