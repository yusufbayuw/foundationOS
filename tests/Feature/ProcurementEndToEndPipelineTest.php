<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use DomainException;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Finance\Models\JournalEntry;
use Modules\Procurement\Exceptions\ThreeWayMatchException;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\VendorBill;
use Modules\Procurement\Services\GoodsReceiptAutoCreationService;
use Modules\Procurement\Services\VendorBillAutoCreationService;
use Tests\TestCase;

class ProcurementEndToEndPipelineTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_partial_receipt_marks_po_partial_then_full_receipt_marks_received(): void
    {
        [$tenant, $po] = $this->makePoWithItems([
            ['Monitor', 4, 5_000_000],
            ['Keyboard', 6, 1_000_000],
        ]);

        $poItems = $po->items;

        // Partial: receive only 2 of 4 monitors, none of the keyboards.
        app(GoodsReceiptAutoCreationService::class)->receive(
            $po,
            quantities: [
                $poItems[0]->id => 2,
                $poItems[1]->id => 0,
            ],
            receivedBy: null,
        );

        $this->assertSame('partial', $po->fresh()->status);
        $this->assertSame(2, (int) $poItems[0]->fresh()->quantity_received);

        // Receive the rest: 2 more monitors + all 6 keyboards.
        app(GoodsReceiptAutoCreationService::class)->receive(
            $po,
            quantities: [
                $poItems[0]->id => 2,
                $poItems[1]->id => 6,
            ],
        );

        $this->assertSame('received', $po->fresh()->status);
        $this->assertSame(4, (int) $poItems[0]->fresh()->quantity_received);
        $this->assertSame(6, (int) $poItems[1]->fresh()->quantity_received);
    }

    public function test_full_pipeline_receipt_to_bill_posts_balanced_draft_journal(): void
    {
        [$tenant, $po] = $this->makePoWithItems([
            ['Monitor', 3, 5_000_000],
        ]);

        $receipt = app(GoodsReceiptAutoCreationService::class)->receive($po);
        $bill = app(VendorBillAutoCreationService::class)->createFromGoodsReceipt($receipt);

        $this->assertNotNull($bill->journal_entry_id);
        $this->assertSame(15_000_000.0, (float) $bill->total_amount);
        $this->assertSame(1, $bill->items()->count());

        $journal = JournalEntry::withoutTenantScope()->find($bill->journal_entry_id);
        $this->assertNotNull($journal);
        $this->assertSame((int) $tenant->id, (int) $journal->tenant_id);
        $this->assertTrue((bool) $journal->is_balanced);
        $this->assertFalse((bool) $journal->is_posted, 'auto-journal stays draft until Finance posts it');
        $this->assertEqualsWithDelta(15_000_000.0, (float) $journal->total_debit, 0.01);
        $this->assertEqualsWithDelta(15_000_000.0, (float) $journal->total_credit, 0.01);
    }

    public function test_three_way_mismatch_is_rejected_with_clear_errors(): void
    {
        [, $po] = $this->makePoWithItems([
            ['Monitor', 2, 5_000_000],
        ]);

        $receipt = app(GoodsReceiptAutoCreationService::class)->receive($po);

        try {
            app(VendorBillAutoCreationService::class)->createFromGoodsReceipt(
                $receipt,
                billLines: [[
                    'purchase_order_item_id' => $po->items->first()->id,
                    'quantity' => 5, // received only 2
                    'unit_price' => 5_000_000,
                ]],
            );
            $this->fail('Expected ThreeWayMatchException');
        } catch (ThreeWayMatchException $e) {
            $this->assertNotEmpty($e->errors);
            $this->assertStringContainsString('exceeds received quantity', implode(' ', $e->errors));
        }

        $this->assertSame(0, VendorBill::withoutTenantScope()->count());
    }

    public function test_three_way_mismatch_rejects_price_outside_tolerance(): void
    {
        [, $po] = $this->makePoWithItems([
            ['Server', 1, 50_000_000],
        ]);
        $receipt = app(GoodsReceiptAutoCreationService::class)->receive($po);

        $this->expectException(ThreeWayMatchException::class);

        app(VendorBillAutoCreationService::class)->createFromGoodsReceipt(
            $receipt,
            billLines: [[
                'purchase_order_item_id' => $po->items->first()->id,
                'quantity' => 1,
                'unit_price' => 60_000_000, // 20% above PO price
            ]],
        );
    }

    public function test_fully_received_purchase_order_cannot_create_an_empty_second_receipt(): void
    {
        [, $po] = $this->makePoWithItems([['Monitor', 2, 5_000_000]]);

        $service = app(GoodsReceiptAutoCreationService::class);
        $service->receive($po);

        try {
            $service->receive($po);
            $this->fail('Expected rejection when no units remain on the PO.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('at least one item', $exception->getMessage());
        }

        $this->assertSame(1, GoodsReceipt::withoutTenantScope()
            ->where('purchase_order_id', $po->id)
            ->count());
    }

    public function test_auto_vendor_bill_replay_returns_existing_bill_without_another_journal(): void
    {
        [, $po] = $this->makePoWithItems([['Monitor', 2, 5_000_000]]);
        $receipt = app(GoodsReceiptAutoCreationService::class)->receive($po);

        $service = app(VendorBillAutoCreationService::class);
        $first = $service->createFromGoodsReceipt($receipt);
        $second = $service->createFromGoodsReceipt($receipt);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, VendorBill::withoutTenantScope()
            ->where('goods_receipt_id', $receipt->id)
            ->count());
        $this->assertSame(1, JournalEntry::withoutTenantScope()
            ->where('tenant_id', $receipt->tenant_id)
            ->where('entry_number', 'JE-'.$first->bill_number)
            ->count());
    }

    public function test_explicit_rebilling_is_rejected_instead_of_silently_replacing_existing_bill(): void
    {
        [, $po] = $this->makePoWithItems([['Monitor', 2, 5_000_000]]);
        $receipt = app(GoodsReceiptAutoCreationService::class)->receive($po);

        app(VendorBillAutoCreationService::class)->createFromGoodsReceipt($receipt);

        $this->expectException(ThreeWayMatchException::class);

        app(VendorBillAutoCreationService::class)->createFromGoodsReceipt($receipt, [[
            'purchase_order_item_id' => $po->items->first()->id,
            'quantity' => 1,
            'unit_price' => 5_000_000,
        ]]);
    }

    public function test_three_way_match_rejects_empty_negative_and_duplicate_bill_lines(): void
    {
        [, $po] = $this->makePoWithItems([['Monitor', 2, 5_000_000]]);
        $receipt = app(GoodsReceiptAutoCreationService::class)->receive($po);
        $poItemId = $po->items->first()->id;

        $valid = [
            'purchase_order_item_id' => $poItemId,
            'quantity' => 1,
            'unit_price' => 5_000_000,
        ];

        foreach ([
            [],
            [array_merge($valid, ['quantity' => -1])],
            [array_merge($valid, ['quantity' => 0])],
            [array_merge($valid, ['unit_price' => -1])],
            [$valid, $valid],
        ] as $invalidLines) {
            try {
                app(VendorBillAutoCreationService::class)->createFromGoodsReceipt($receipt, $invalidLines);
                $this->fail('Expected a three-way match error for invalid bill lines.');
            } catch (ThreeWayMatchException $exception) {
                $this->assertNotEmpty($exception->errors);
            }
        }

        $this->assertSame(0, VendorBill::withoutTenantScope()
            ->where('goods_receipt_id', $receipt->id)
            ->count());
    }

    public function test_auto_billing_requires_a_confirmed_goods_receipt(): void
    {
        [, $po] = $this->makePoWithItems([['Monitor', 1, 5_000_000]]);
        $receipt = app(GoodsReceiptAutoCreationService::class)->receive($po);
        $receipt->forceFill(['status' => 'draft'])->save();

        $this->expectException(ThreeWayMatchException::class);

        app(VendorBillAutoCreationService::class)->createFromGoodsReceipt($receipt);
    }

    /**
     * @param  array<int, array{0:string,1:int,2:int|float}>  $itemSpecs
     * @return array{0:Tenant,1:PurchaseOrder}
     */
    protected function makePoWithItems(array $itemSpecs): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'e2e-proc-test'],
            ['name' => 'E2E', 'included_modules' => ['core', 'procurement', 'finance']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "e2e-{$rand}",
            'name' => 'E2E Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "e2e-org-{$rand}",
            'name' => 'E2E Org',
        ]);

        $vendor = Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'E2E Vendor',
        ]);

        $po = PurchaseOrder::query()->create([
            'tenant_id' => $tenant->id,
            'vendor_id' => $vendor->id,
            'po_number' => "PO-{$rand}",
            'po_date' => now()->toDateString(),
            'status' => 'draft',
            'currency' => 'IDR',
            'exchange_rate' => 1,
        ]);

        foreach ($itemSpecs as [$desc, $qty, $price]) {
            PurchaseOrderItem::query()->create([
                'tenant_id' => $tenant->id,
                'purchase_order_id' => $po->id,
                'description' => $desc,
                'quantity' => $qty,
                'unit_price' => $price,
                'line_total' => $qty * $price,
                'unit_of_measure' => 'unit',
            ]);
        }

        return [$tenant, $po->fresh(['items'])];
    }
}
