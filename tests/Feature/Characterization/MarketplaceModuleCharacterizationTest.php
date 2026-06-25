<?php

namespace Tests\Feature\Characterization;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Marketplace\Models\MarketplaceOrder;
use Modules\Marketplace\Models\Seller;
use Modules\Marketplace\Services\MarketplaceOrderService;
use Modules\Marketplace\Services\SellerRegistrationService;
use RuntimeException;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

/**
 * Characterization tests for Marketplace seller/order scoping behavior.
 */
class MarketplaceModuleCharacterizationTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_orders_for_seller_returns_only_matching_seller_rows(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'marketplace']);

        $sellerA = app(SellerRegistrationService::class)->register($tenant->id, $organization->id, 'SA', 'Seller A');
        $sellerB = app(SellerRegistrationService::class)->register($tenant->id, $organization->id, 'SB', 'Seller B');

        $orderA = $this->createOrder($tenant->id, $organization->id, $sellerA->id, 'ORD-A');
        $this->createOrder($tenant->id, $organization->id, $sellerB->id, 'ORD-B');

        $ids = app(MarketplaceOrderService::class)
            ->ordersForSeller($sellerA)
            ->pluck('id')
            ->all();

        $this->assertSame([$orderA->id], $ids);
    }

    public function test_assert_seller_isolation_throws_for_foreign_order(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'marketplace']);

        $sellerA = app(SellerRegistrationService::class)->register($tenant->id, $organization->id, 'S1', 'Seller One');
        $sellerB = app(SellerRegistrationService::class)->register($tenant->id, $organization->id, 'S2', 'Seller Two');

        $orderForB = $this->createOrder($tenant->id, $organization->id, $sellerB->id, 'ORD-FOR-B');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Order does not belong to this seller.');

        app(MarketplaceOrderService::class)->assertSellerIsolation($sellerA, $orderForB);
    }

    public function test_seller_factory_defaults_verification_status_to_pending(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'marketplace']);

        $seller = Seller::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertSame('pending', $seller->verification_status);
        $this->assertSame('active', $seller->status);
    }

    private function createOrder(int $tenantId, int $organizationId, int $sellerId, string $code): MarketplaceOrder
    {
        return MarketplaceOrder::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'organization_id' => $organizationId,
            'seller_id' => $sellerId,
            'code' => $code,
            'name' => 'Order '.$code,
            'status' => 'active',
        ]);
    }
}
