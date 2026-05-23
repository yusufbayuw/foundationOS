<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Inventory\Enums\StockMoveType;
use Modules\Inventory\Models\StockItem;
use Modules\Inventory\Models\StockLevel;
use Modules\Inventory\Models\StockMove;
use Modules\Inventory\Services\StockMoveService;
use Modules\Inventory\Services\WarehouseResolver;
use Modules\Procurement\Models\ProcurementItem;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Services\GoodsReceiptAutoCreationService;
use Tests\TestCase;

class InventoryProcurementIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_goods_receipt_creates_stock_in_moves_and_levels(): void
    {
        [$tenant, $po, $procurementItem] = $this->makePoWithProcurementItem('Paper Ream', 10, 50_000);

        $receipt = app(GoodsReceiptAutoCreationService::class)->receive($po);

        $moves = StockMove::withoutTenantScope()
            ->where('tenant_id', $tenant->id)
            ->where('move_type', StockMoveType::In)
            ->get();

        $this->assertCount(1, $moves);
        $this->assertEqualsWithDelta(10.0, (float) $moves->first()->quantity, 0.01);

        $warehouse = app(WarehouseResolver::class)->defaultForTenant((int) $tenant->id);

        $level = StockLevel::withoutTenantScope()
            ->where('warehouse_id', $warehouse->id)
            ->whereHas('stockItem', fn ($q) => $q->where('procurement_item_id', $procurementItem->id))
            ->first();

        $this->assertNotNull($level);
        $this->assertEqualsWithDelta(10.0, (float) $level->quantity_on_hand, 0.01);
        $this->assertSame((int) $receipt->items->first()->id, (int) $moves->first()->reference_id);
    }

    public function test_stock_in_posts_balanced_journal_lines_when_coa_exists(): void
    {
        [$tenant, $po] = $this->makePoWithProcurementItem('Toner', 2, 100_000);
        $org = Organization::withoutTenantScope()->where('tenant_id', $tenant->id)->first();

        ChartOfAccount::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'code' => '1401',
            'name' => 'Persediaan Barang',
            'type' => 'asset',
            'is_active' => true,
        ]);

        ChartOfAccount::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'code' => '2101',
            'name' => 'Hutang GR/IR',
            'type' => 'liability',
            'is_active' => true,
        ]);

        app(GoodsReceiptAutoCreationService::class)->receive($po);

        $move = StockMove::withoutTenantScope()->where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($move->journal_entry_id);

        $lines = JournalEntryLine::withoutTenantScope()
            ->where('journal_entry_id', $move->journal_entry_id)
            ->get();

        $this->assertCount(2, $lines);
        $this->assertEqualsWithDelta(
            $lines->sum('debit'),
            $lines->sum('credit'),
            0.01,
        );
    }

    public function test_stock_out_reduces_on_hand(): void
    {
        [$tenant, $po, $procurementItem] = $this->makePoWithProcurementItem('Ink', 5, 20_000);

        app(GoodsReceiptAutoCreationService::class)->receive($po);

        $warehouse = app(WarehouseResolver::class)->defaultForTenant((int) $tenant->id);
        $stockItem = StockItem::withoutTenantScope()
            ->where('procurement_item_id', $procurementItem->id)
            ->first();

        app(StockMoveService::class)->commitOutbound($warehouse, $stockItem, 2);

        $level = StockLevel::withoutTenantScope()
            ->where('warehouse_id', $warehouse->id)
            ->where('stock_item_id', $stockItem->id)
            ->first();

        $this->assertEqualsWithDelta(3.0, (float) $level->quantity_on_hand, 0.01);

        $outMove = StockMove::withoutTenantScope()
            ->where('stock_item_id', $stockItem->id)
            ->where('move_type', StockMoveType::Out)
            ->first();

        $this->assertNotNull($outMove);
    }

    public function test_insufficient_stock_out_is_rejected(): void
    {
        [$tenant, $po, $procurementItem] = $this->makePoWithProcurementItem('Stapler', 1, 30_000);

        app(GoodsReceiptAutoCreationService::class)->receive($po);

        $warehouse = app(WarehouseResolver::class)->defaultForTenant((int) $tenant->id);
        $stockItem = StockItem::withoutTenantScope()
            ->where('procurement_item_id', $procurementItem->id)
            ->first();

        $this->expectException(\RuntimeException::class);
        app(StockMoveService::class)->commitOutbound($warehouse, $stockItem, 5);
    }

    /**
     * @return array{0:Tenant,1:PurchaseOrder,2:ProcurementItem}
     */
    protected function makePoWithProcurementItem(string $name, int $qty, float $price): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'e2e-inv-test'],
            ['name' => 'E2E Inv', 'included_modules' => ['core', 'procurement', 'finance', 'inventory']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "inv-{$rand}",
            'name' => 'Inventory Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "inv-org-{$rand}",
            'name' => 'Inventory Org',
        ]);

        $procurementItem = ProcurementItem::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'code' => "PI-{$rand}",
            'name' => $name,
            'unit_of_measure' => 'unit',
            'estimated_price' => $price,
            'is_active' => true,
        ]);

        $vendor = Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Inventory Vendor',
        ]);

        $po = PurchaseOrder::query()->create([
            'tenant_id' => $tenant->id,
            'vendor_id' => $vendor->id,
            'po_number' => "PO-INV-{$rand}",
            'po_date' => now()->toDateString(),
            'status' => 'draft',
            'currency' => 'IDR',
            'exchange_rate' => 1,
        ]);

        PurchaseOrderItem::query()->create([
            'tenant_id' => $tenant->id,
            'purchase_order_id' => $po->id,
            'procurement_item_id' => $procurementItem->id,
            'description' => $name,
            'quantity' => $qty,
            'unit_price' => $price,
            'line_total' => $qty * $price,
            'unit_of_measure' => 'unit',
        ]);

        return [$tenant, $po->fresh(['items']), $procurementItem];
    }
}
