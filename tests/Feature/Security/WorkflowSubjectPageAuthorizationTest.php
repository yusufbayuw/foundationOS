<?php

namespace Tests\Feature\Security;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Services\WorkflowSubjectPageService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class WorkflowSubjectPageAuthorizationTest extends TestCase
{
    use CreatesTenantForTests;
    use RefreshDatabase;

    public function test_unprivileged_user_cannot_start_approval_workflow(): void
    {
        [$tenant, , $user, $requisition] = $this->seedPurchaseRequisition();

        $this->setupProcurementPilot($tenant, $user);

        $this->expectException(AuthorizationException::class);

        app(WorkflowSubjectPageService::class)->startApprovalWorkflow($requisition, $user, $tenant);
    }

    public function test_locked_requisition_cannot_start_approval_workflow(): void
    {
        [$tenant, , , $requisition] = $this->seedPurchaseRequisition(status: 'approved');
        $superAdmin = User::factory()->superAdmin()->create();

        $this->expectException(AuthorizationException::class);

        app(WorkflowSubjectPageService::class)->startApprovalWorkflow($requisition, $superAdmin, $tenant);
    }

    public function test_cross_tenant_subject_is_rejected(): void
    {
        [$tenantA, , $owner, $requisition] = $this->seedPurchaseRequisition();
        $tenantB = $this->makeTenantContext(['core', 'procurement', 'workflow'])['tenant'];
        $superAdmin = User::factory()->superAdmin()->create();

        $this->setupProcurementPilot($tenantA, $owner);

        $this->expectException(AuthorizationException::class);

        app(WorkflowSubjectPageService::class)->startApprovalWorkflow($requisition, $superAdmin, $tenantB);
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User, 3: PurchaseRequisition}
     */
    private function seedPurchaseRequisition(string $status = 'draft'): array
    {
        $context = $this->makeTenantContext(['core', 'procurement', 'workflow']);

        $requisition = PurchaseRequisition::create([
            'tenant_id' => $context['tenant']->id,
            'user_id' => $context['user']->id,
            'requested_by' => $context['user']->id,
            'request_number' => 'PR-SEC-'.Str::upper(Str::random(4)),
            'request_date' => now()->toDateString(),
            'status' => $status,
            'total_estimated_amount' => 1_000_000,
        ]);

        return [$context['tenant'], $context['organization'], $context['user'], $requisition];
    }

    private function setupProcurementPilot(Tenant $tenant, User $user): void
    {
        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $tenant->id,
            '--manager' => $user->id,
            '--finance' => $user->id,
            '--executive' => $user->id,
        ])->assertSuccessful();
    }
}
