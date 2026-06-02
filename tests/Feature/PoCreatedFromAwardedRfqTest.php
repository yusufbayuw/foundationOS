<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Procurement\Exceptions\RfqAwardException;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Models\RfqItem;
use Modules\Procurement\Models\RfqVendor;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Services\PurchaseOrderAutoCreationService;
use Tests\TestCase;

class PoCreatedFromAwardedRfqTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_award_creates_draft_po_inheriting_tenant_and_currency(): void
    {
        [$tenant, $rfq, $awardedVendor, $secondVendor] = $this->makeRfqWithVendors();

        $po = app(PurchaseOrderAutoCreationService::class)
            ->awardToVendor($awardedVendor, awardReason: 'best price-quality ratio');

        $this->assertSame((int) $tenant->id, (int) $po->tenant_id);
        $this->assertSame('IDR', $po->currency);
        $this->assertSame('draft', $po->status);
        $this->assertSame($awardedVendor->vendor_id, $po->vendor_id);
        $this->assertEqualsWithDelta(9_500_000.0, (float) $po->subtotal, 0.01);
        $this->assertEqualsWithDelta(9_500_000.0, (float) $po->total_amount, 0.01);

        $this->assertSame(2, $po->items()->count());

        // RFQ + winning vendor are locked, sibling vendors demoted.
        $this->assertSame('awarded', $rfq->fresh()->status);
        $this->assertTrue($awardedVendor->fresh()->is_awarded);
        $this->assertFalse($secondVendor->fresh()->is_awarded);

        $this->assertStringStartsWith('PO-', $po->po_number);
        $this->assertStringContainsString((string) $rfq->rfq_number, $po->po_number);
    }

    public function test_award_is_idempotent_returns_existing_draft(): void
    {
        [, , $awardedVendor] = $this->makeRfqWithVendors();

        $first = app(PurchaseOrderAutoCreationService::class)->awardToVendor($awardedVendor);
        $second = app(PurchaseOrderAutoCreationService::class)->awardToVendor($awardedVendor);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, PurchaseOrder::withoutTenantScope()
            ->where('request_for_quotation_id', $awardedVendor->request_for_quotation_id)
            ->count());
    }

    public function test_cannot_award_a_second_vendor_after_lock(): void
    {
        [, , $awardedVendor, $secondVendor] = $this->makeRfqWithVendors();

        app(PurchaseOrderAutoCreationService::class)->awardToVendor($awardedVendor);

        $this->expectException(RfqAwardException::class);

        app(PurchaseOrderAutoCreationService::class)->awardToVendor($secondVendor);
    }

    /**
     * @return array{0:Tenant,1:RequestForQuotation,2:RfqVendor,3:RfqVendor}
     */
    protected function makeRfqWithVendors(): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'po-auto-test'],
            ['name' => 'PO Auto Test', 'included_modules' => ['core', 'procurement']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "po-auto-{$rand}",
            'name' => 'PO Auto Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $user = User::create([
            'name' => 'Procurement Officer',
            'email' => "po-officer-{$rand}@example.com",
            'password' => 'password',
        ]);

        $pr = PurchaseRequisition::query()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'requested_by' => $user->id,
            'request_number' => "PR-{$rand}",
            'request_date' => now()->toDateString(),
            'priority' => 'normal',
            'total_items' => 2,
            'total_estimated_amount' => 10_000_000,
            'status' => 'approved',
        ]);

        $rfq = RequestForQuotation::query()->create([
            'tenant_id' => $tenant->id,
            'purchase_requisition_id' => $pr->id,
            'created_by' => $user->id,
            'rfq_number' => "RFQ-{$rand}",
            'rfq_date' => now()->toDateString(),
            'total_estimated_budget' => 10_000_000,
            'currency' => 'IDR',
            'status' => 'open',
        ]);

        foreach (['Monitor', 'Keyboard'] as $i => $desc) {
            RfqItem::query()->create([
                'tenant_id' => $tenant->id,
                'request_for_quotation_id' => $rfq->id,
                'description' => $desc,
                'quantity' => $i === 0 ? 2 : 5,
                'unit_of_measure' => 'unit',
                'estimated_budget' => 5_000_000,
            ]);
        }

        $vendorA = Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Vendor A',
        ]);
        $vendorB = Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Vendor B',
        ]);

        $awarded = RfqVendor::query()->create([
            'tenant_id' => $tenant->id,
            'request_for_quotation_id' => $rfq->id,
            'vendor_id' => $vendorA->id,
            'status' => 'responded',
            'quotation_amount' => 9_500_000,
            'is_shortlisted' => true,
        ]);

        $second = RfqVendor::query()->create([
            'tenant_id' => $tenant->id,
            'request_for_quotation_id' => $rfq->id,
            'vendor_id' => $vendorB->id,
            'status' => 'responded',
            'quotation_amount' => 10_200_000,
        ]);

        return [$tenant, $rfq, $awarded, $second];
    }
}
