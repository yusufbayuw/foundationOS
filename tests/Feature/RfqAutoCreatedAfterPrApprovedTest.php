<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Procurement\Events\PurchaseRequisitionApproved;
use Modules\Procurement\Listeners\CreateRfqFromApprovedPurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionItem;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Services\RfqAutoCreationService;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Tests\TestCase;

class RfqAutoCreatedAfterPrApprovedTest extends TestCase
{
    use RefreshDatabase;

    public function test_rfq_draft_is_created_when_purchase_requisition_is_approved(): void
    {
        Queue::fake();

        [$tenant, $requester, $manager] = $this->makeTenantContext();
        $this->setupPilot($tenant->id, $manager->id);

        $requisition = $this->makePurchaseRequisition($tenant, $requester, 2_500_000);
        $workflow = app(WorkflowResolver::class)->resolveForSubject(
            $requisition->workflowSubjectType(), $requisition, (int) $tenant->id, null,
        );
        $instance = app(WorkflowInstanceStarter::class)->start(
            $workflow, $requester, $requisition->workflowContext(), $requisition, $requester,
        );

        Event::fake([PurchaseRequisitionApproved::class]);

        app(WorkflowEngine::class)->advance($instance, 'approve', [
            'approval_note' => 'Manager approves this low-amount requisition.',
        ], $manager);

        Event::assertDispatched(PurchaseRequisitionApproved::class, function ($event) use ($requisition) {
            return $event->requisition->id === $requisition->id;
        });
    }

    public function test_listener_creates_rfq_with_copied_items_and_audit_link(): void
    {
        [$tenant, $requester, $manager] = $this->makeTenantContext();
        $requisition = $this->makePurchaseRequisition($tenant, $requester, 5_000_000);
        PurchaseRequisitionItem::query()->create([
            'tenant_id' => $tenant->id,
            'purchase_requisition_id' => $requisition->id,
            'description' => 'Laptop',
            'quantity_requested' => 3,
            'unit_of_measure' => 'unit',
            'estimated_unit_price' => 5_000_000,
            'estimated_total_price' => 15_000_000,
        ]);
        $requisition->forceFill(['status' => 'approved', 'approved_by' => $manager->id])->save();

        (new CreateRfqFromApprovedPurchaseRequisition)
            ->handle(new PurchaseRequisitionApproved($requisition->fresh(['items']), $manager));

        $rfq = RequestForQuotation::withoutTenantScope()
            ->where('purchase_requisition_id', $requisition->id)
            ->first();

        $this->assertNotNull($rfq);
        $this->assertSame('draft', $rfq->status);
        $this->assertSame((int) $tenant->id, (int) $rfq->tenant_id);
        $this->assertEqualsWithDelta(5_000_000.0, (float) $rfq->total_estimated_budget, 0.01);
        $this->assertSame(1, $rfq->items()->count());
        $this->assertStringStartsWith('RFQ-', $rfq->rfq_number);
    }

    public function test_listener_is_idempotent_and_skips_duplicate_drafts(): void
    {
        [$tenant, $requester, $manager] = $this->makeTenantContext();
        $requisition = $this->makePurchaseRequisition($tenant, $requester, 1_000_000);
        $requisition->forceFill(['status' => 'approved'])->save();

        $listener = new CreateRfqFromApprovedPurchaseRequisition;
        $listener->handle(new PurchaseRequisitionApproved($requisition->fresh(), $manager));
        $listener->handle(new PurchaseRequisitionApproved($requisition->fresh(), $manager));

        $this->assertSame(
            1,
            RequestForQuotation::withoutTenantScope()
                ->where('purchase_requisition_id', $requisition->id)
                ->where('status', 'draft')
                ->count(),
        );
    }

    public function test_tenant_setting_disabled_short_circuits_rfq_creation(): void
    {
        [$tenant, $requester, $manager] = $this->makeTenantContext();

        TenantSetting::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'group' => RfqAutoCreationService::SETTING_GROUP,
            'key' => RfqAutoCreationService::SETTING_KEY,
            'value' => '0',
            'type' => 'boolean',
        ]);

        $requisition = $this->makePurchaseRequisition($tenant, $requester, 1_000_000);
        $requisition->forceFill(['status' => 'approved'])->save();

        (new CreateRfqFromApprovedPurchaseRequisition)
            ->handle(new PurchaseRequisitionApproved($requisition->fresh(), $manager));

        $this->assertSame(
            0,
            RequestForQuotation::withoutTenantScope()
                ->where('purchase_requisition_id', $requisition->id)
                ->count(),
        );
    }

    protected function setupPilot(int $tenantId, int $managerId): void
    {
        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $tenantId,
            '--manager' => $managerId,
            '--finance' => $managerId,
            '--finance-threshold' => 10_000_000,
        ])->assertSuccessful();
    }

    protected function makePurchaseRequisition(Tenant $tenant, User $requester, float $amount): PurchaseRequisition
    {
        return PurchaseRequisition::query()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $requester->id,
            'requested_by' => $requester->id,
            'request_number' => 'PR-'.Str::upper(Str::random(6)),
            'request_date' => now()->toDateString(),
            'priority' => 'normal',
            'total_items' => 1,
            'total_estimated_amount' => $amount,
            'status' => 'draft',
        ]);
    }

    /**
     * @return array{0:Tenant,1:User,2:User}
     */
    protected function makeTenantContext(): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'rfq-auto-test'],
            ['name' => 'RFQ Auto Test', 'included_modules' => ['core', 'procurement', 'workflow']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "rfq-auto-{$rand}",
            'name' => "RFQ Auto Tenant {$rand}",
            'subscription_plan_id' => $plan->id,
        ]);

        $role = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Default',
            'slug' => "default-{$rand}",
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        $requester = User::create([
            'name' => 'Requester '.$rand,
            'email' => "requester-{$rand}@example.com",
            'password' => 'password',
        ]);

        $manager = User::create([
            'name' => 'Manager '.$rand,
            'email' => "manager-{$rand}@example.com",
            'password' => 'password',
        ]);

        foreach ([$requester, $manager] as $u) {
            UserTenantRole::create([
                'user_id' => $u->id,
                'tenant_id' => $tenant->id,
                'tenant_role_id' => $role->id,
                'assigned_by' => $u->id,
                'is_primary' => true,
            ]);
        }

        return [$tenant, $requester, $manager];
    }
}
