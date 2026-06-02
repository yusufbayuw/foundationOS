<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Inventory\Models\StockMove;
use Modules\Inventory\Models\Warehouse;
use Modules\Monitoring\Models\AuditLog;
use Modules\Procurement\Models\ProcurementItem;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Services\GoodsReceiptAutoCreationService;
use Tests\TestCase;

class MonitoringAuditTrailTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_warehouse_create_writes_audit_log_with_morph_alias(): void
    {
        $tenant = $this->makeTenant();
        $user = User::factory()->create();

        $this->actingAs($user);

        $warehouse = Warehouse::query()->create([
            'tenant_id' => $tenant->id,
            'code' => 'WH-AUDIT',
            'name' => 'Audit Warehouse',
            'is_active' => true,
        ]);

        $log = AuditLog::withoutTenantScope()
            ->where('auditable_type', 'warehouse')
            ->where('auditable_id', $warehouse->id)
            ->where('action', 'warehouse.created')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame((int) $user->id, (int) $log->user_id);
        $this->assertSame('Audit Warehouse', $log->new_values['name'] ?? null);
    }

    public function test_goods_receipt_and_stock_move_emit_audit_trail(): void
    {
        $tenant = $this->makeTenant();
        $user = User::factory()->create();
        $this->actingAs($user);

        $procurementItem = ProcurementItem::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'code' => 'AUD-ITEM',
            'name' => 'Audit Item',
            'is_active' => true,
        ]);

        $vendor = Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Audit Vendor',
        ]);

        $po = PurchaseOrder::query()->create([
            'tenant_id' => $tenant->id,
            'vendor_id' => $vendor->id,
            'po_number' => 'PO-AUDIT',
            'po_date' => now()->toDateString(),
            'status' => 'draft',
            'currency' => 'IDR',
            'exchange_rate' => 1,
        ]);

        PurchaseOrderItem::query()->create([
            'tenant_id' => $tenant->id,
            'purchase_order_id' => $po->id,
            'procurement_item_id' => $procurementItem->id,
            'description' => 'Audit Item',
            'quantity' => 3,
            'unit_price' => 10_000,
            'line_total' => 30_000,
        ]);

        $receipt = app(GoodsReceiptAutoCreationService::class)->receive($po->fresh(['items']));

        $this->assertTrue(
            AuditLog::withoutTenantScope()
                ->where('auditable_type', 'goods_receipt')
                ->where('auditable_id', $receipt->id)
                ->where('action', 'goods_receipt.created')
                ->exists(),
        );

        $move = StockMove::withoutTenantScope()->where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($move);

        $this->assertTrue(
            AuditLog::withoutTenantScope()
                ->where('auditable_type', 'stock_move')
                ->where('auditable_id', $move->id)
                ->where('action', 'stock_move.created')
                ->exists(),
        );
    }

    protected function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'audit-trail-test'],
            ['name' => 'Audit', 'included_modules' => ['core', 'inventory', 'procurement', 'monitoring']],
        );

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'audit-'.Str::random(4),
            'name' => 'Audit Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'audit-org',
            'name' => 'Audit Org',
        ]);

        return $tenant;
    }
}
