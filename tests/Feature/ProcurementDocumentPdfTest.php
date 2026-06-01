<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\GoodsReceiptItem;
use Modules\Procurement\Models\ProcurementCategory;
use Modules\Procurement\Models\ProcurementItem;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionItem;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Models\RfqItem;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\VendorBill;
use Modules\Procurement\Models\VendorBillItem;
use Tests\Concerns\InteractsWithPdfDocuments;
use Tests\TestCase;

class ProcurementDocumentPdfTest extends TestCase
{
    use InteractsWithPdfDocuments;
    use RefreshDatabase;

    public function test_guest_cannot_download_purchase_requisition_pdf(): void
    {
        [$tenant, , $user] = $this->makeProcurementTenantContext();
        $requisition = $this->makeApprovedPurchaseRequisition($tenant, $user);

        $this->getJson(route('procurement.purchase-requisitions.pdf', $requisition))
            ->assertUnauthorized();
    }

    public function test_super_admin_can_download_approved_purchase_requisition_pdf(): void
    {
        [$tenant, , $user] = $this->makeProcurementTenantContext();
        $requisition = $this->makeApprovedPurchaseRequisition($tenant, $user);

        $this->actingAs($user)
            ->get(route('procurement.purchase-requisitions.pdf', $requisition))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_draft_purchase_requisition_pdf_is_forbidden(): void
    {
        [$tenant, , $user] = $this->makeProcurementTenantContext();

        $requisition = PurchaseRequisition::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'requested_by' => $user->id,
            'request_number' => 'PR-DRAFT',
            'request_date' => '2026-08-01',
            'status' => 'draft',
        ]);

        $this->actingAs($user)
            ->get(route('procurement.purchase-requisitions.pdf', $requisition))
            ->assertForbidden();
    }

    public function test_super_admin_can_download_approved_purchase_order_pdf(): void
    {
        [, , $user, $purchaseOrder] = $this->makeProcurementChain(purchaseOrderStatus: 'approved');

        $this->actingAs($user)
            ->get(route('procurement.purchase-orders.pdf', $purchaseOrder))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_draft_purchase_order_pdf_is_forbidden(): void
    {
        [, , $user, $purchaseOrder] = $this->makeProcurementChain(purchaseOrderStatus: 'draft');

        $this->actingAs($user)
            ->get(route('procurement.purchase-orders.pdf', $purchaseOrder))
            ->assertForbidden();
    }

    public function test_super_admin_can_download_confirmed_goods_receipt_pdf(): void
    {
        [$tenant, , $user, , $receipt] = $this->makeProcurementChain();

        $this->actingAs($user)
            ->get(route('procurement.goods-receipts.pdf', $receipt))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_confirmed_vendor_bill_pdf(): void
    {
        [$tenant, , $user, , , $bill] = $this->makeProcurementChain();

        $this->actingAs($user)
            ->get(route('procurement.vendor-bills.pdf', $bill))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_rfq_pdf(): void
    {
        [$tenant, , $user, , , , $rfq] = $this->makeProcurementChain(rfqStatus: 'sent');

        $this->actingAs($user)
            ->get(route('procurement.rfqs.pdf', $rfq))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_draft_rfq_pdf_is_forbidden(): void
    {
        [$tenant, , $user, , , , $rfq] = $this->makeProcurementChain(rfqStatus: 'draft');

        $this->actingAs($user)
            ->get(route('procurement.rfqs.pdf', $rfq))
            ->assertForbidden();
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User}
     */
    private function makeProcurementTenantContext(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'procurement-pdf',
            'name' => 'Procurement PDF',
            'included_modules' => ['core', 'procurement'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'proc-pdf',
            'name' => 'Procurement PDF Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Organization',
            'is_main' => true,
        ]);

        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        return [$tenant, $organization, $user];
    }

    private function makeApprovedPurchaseRequisition(Tenant $tenant, User $user): PurchaseRequisition
    {
        $organization = Organization::query()->where('tenant_id', $tenant->id)->first();

        $vendor = Vendor::create([
            'tenant_id' => $tenant->id,
            'code' => 'V-PDF',
            'name' => 'PT Vendor PDF',
        ]);

        $category = ProcurementCategory::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'OFFICE',
            'name' => 'Office Supplies',
        ]);

        $item = ProcurementItem::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'preferred_vendor_id' => $vendor->id,
            'code' => 'ITEM-PDF',
            'name' => 'Printer Paper',
            'estimated_price' => 75000,
        ]);

        $requisition = PurchaseRequisition::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'requested_by' => $user->id,
            'approved_by' => $user->id,
            'request_number' => 'PR-PDF-001',
            'request_date' => '2026-08-01',
            'status' => 'approved',
            'total_estimated_amount' => 750000,
        ]);

        PurchaseRequisitionItem::create([
            'tenant_id' => $tenant->id,
            'purchase_requisition_id' => $requisition->id,
            'procurement_item_id' => $item->id,
            'preferred_vendor_id' => $vendor->id,
            'quantity_requested' => 10,
            'estimated_unit_price' => 75000,
            'estimated_total_price' => 750000,
        ]);

        return $requisition;
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User, 3: PurchaseOrder, 4: GoodsReceipt, 5: VendorBill, 6: RequestForQuotation}
     */
    private function makeProcurementChain(
        string $purchaseOrderStatus = 'approved',
        string $rfqStatus = 'confirmed',
    ): array {
        [$tenant, $organization, $user] = $this->makeProcurementTenantContext();

        $requisition = $this->makeApprovedPurchaseRequisition($tenant, $user);

        $rfq = RequestForQuotation::create([
            'tenant_id' => $tenant->id,
            'purchase_requisition_id' => $requisition->id,
            'created_by' => $user->id,
            'rfq_number' => 'RFQ-PDF-001',
            'rfq_date' => '2026-08-02',
            'status' => $rfqStatus,
        ]);

        $vendor = Vendor::query()->where('tenant_id', $tenant->id)->first();

        $requisitionItem = $requisition->items()->first();

        RfqItem::create([
            'tenant_id' => $tenant->id,
            'request_for_quotation_id' => $rfq->id,
            'procurement_item_id' => $requisitionItem->procurement_item_id,
            'description' => 'Printer Paper',
            'quantity' => 10,
            'estimated_budget' => 750000,
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'tenant_id' => $tenant->id,
            'request_for_quotation_id' => $rfq->id,
            'vendor_id' => $vendor->id,
            'approved_by' => $user->id,
            'po_number' => 'PO-PDF-001',
            'po_date' => '2026-08-03',
            'total_amount' => 750000,
            'status' => $purchaseOrderStatus,
        ]);

        $purchaseOrderItem = PurchaseOrderItem::create([
            'tenant_id' => $tenant->id,
            'purchase_order_id' => $purchaseOrder->id,
            'purchase_requisition_item_id' => $requisitionItem->id,
            'procurement_item_id' => $requisitionItem->procurement_item_id,
            'quantity' => 10,
            'unit_price' => 75000,
            'line_total' => 750000,
        ]);

        $receipt = GoodsReceipt::create([
            'tenant_id' => $tenant->id,
            'purchase_order_id' => $purchaseOrder->id,
            'received_by' => $user->id,
            'receipt_number' => 'GR-PDF-001',
            'receipt_date' => '2026-08-04',
            'status' => 'confirmed',
        ]);

        $receiptItem = GoodsReceiptItem::create([
            'tenant_id' => $tenant->id,
            'goods_receipt_id' => $receipt->id,
            'purchase_order_item_id' => $purchaseOrderItem->id,
            'quantity_received' => 10,
            'quantity_accepted' => 10,
            'unit_price' => 75000,
            'total_amount' => 750000,
        ]);

        $bill = VendorBill::create([
            'tenant_id' => $tenant->id,
            'vendor_id' => $vendor->id,
            'purchase_order_id' => $purchaseOrder->id,
            'goods_receipt_id' => $receipt->id,
            'processed_by' => $user->id,
            'bill_number' => 'BILL-PDF-001',
            'bill_date' => '2026-08-05',
            'total_amount' => 750000,
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
        ]);

        VendorBillItem::create([
            'tenant_id' => $tenant->id,
            'vendor_bill_id' => $bill->id,
            'purchase_order_item_id' => $purchaseOrderItem->id,
            'goods_receipt_item_id' => $receiptItem->id,
            'quantity' => 10,
            'unit_price' => 75000,
            'line_total' => 750000,
        ]);

        return [$tenant, $organization, $user, $purchaseOrder, $receipt, $bill, $rfq];
    }
}
