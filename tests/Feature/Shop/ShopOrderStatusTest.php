<?php

namespace Tests\Feature\Shop;

use App\Enums\ShopOrderStatus;
use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Marketplace\Models\MarketplaceOrder;
use Modules\MerchOrder\Models\MerchOrder;
use Tests\TestCase;

class ShopOrderStatusTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_valid_order_status_transitions_are_applied(): void
    {
        $tenant = $this->makeTenant();
        $order = MerchOrder::create([
            'tenant_id' => $tenant->id,
            'name' => 'Uniform order',
            'status' => ShopOrderStatus::Paid,
        ]);

        $order->markReadyForPickup();
        $this->assertSame(ShopOrderStatus::ReadyForPickup, $order->fresh()->status);

        $order->fresh()->markPickedUp();
        $this->assertSame(ShopOrderStatus::PickedUp, $order->fresh()->status);
    }

    public function test_invalid_order_status_transition_is_rejected(): void
    {
        $tenant = $this->makeTenant();
        $order = MarketplaceOrder::create([
            'tenant_id' => $tenant->id,
            'name' => 'Marketplace order',
            'status' => ShopOrderStatus::PendingPayment,
        ]);

        $this->expectException(InvalidArgumentException::class);

        $order->markPickedUp();
    }

    public function test_reject_requires_valid_transition_and_stores_reason(): void
    {
        $tenant = $this->makeTenant();
        $order = MerchOrder::create([
            'tenant_id' => $tenant->id,
            'name' => 'Book order',
            'status' => ShopOrderStatus::Paid,
        ]);

        $order->reject('Stock is unavailable.');

        $this->assertSame(ShopOrderStatus::Rejected, $order->fresh()->status);
        $this->assertSame('Stock is unavailable.', $order->fresh()->rejection_reason);
    }

    public function test_user_only_sees_their_own_shop_orders(): void
    {
        [$tenant, $user, $token] = $this->makeAuthenticatedTenantUser();
        $otherUser = User::factory()->create();

        $myMarketplaceOrder = MarketplaceOrder::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'code' => 'MP-MINE',
            'name' => 'My marketplace order',
            'status' => ShopOrderStatus::Paid,
        ]);

        $myMerchOrder = MerchOrder::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'code' => 'MO-MINE',
            'name' => 'My merch order',
            'status' => ShopOrderStatus::ReadyForPickup,
        ]);

        MarketplaceOrder::create([
            'tenant_id' => $tenant->id,
            'user_id' => $otherUser->id,
            'code' => 'MP-OTHER',
            'name' => 'Other marketplace order',
            'status' => ShopOrderStatus::Paid,
        ]);

        $response = $this->withToken($token)->getJson('/api/v1/app/shop/orders/me');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $myMarketplaceOrder->id)
            ->assertJsonPath('data.0.type', 'marketplace')
            ->assertJsonPath('data.1.id', $myMerchOrder->id)
            ->assertJsonPath('data.1.type', 'merch')
            ->assertJsonMissing(['code' => 'MP-OTHER']);
    }

    private function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'shop-plan-'.Str::random(6),
            'name' => 'Shop Plan',
            'included_modules' => ['core', 'marketplace', 'merchorder'],
        ]);

        $user = User::factory()->create();

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'shop-tenant-'.Str::random(6),
            'name' => 'Shop Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        app(CurrentTenant::class)->set($tenant);

        return $tenant;
    }

    /**
     * @return array{0: Tenant, 1: User, 2: string}
     */
    private function makeAuthenticatedTenantUser(): array
    {
        $tenant = $this->makeTenant();
        $user = User::factory()->create();
        $created = $user->createToken('shop-token');
        $token = PersonalAccessToken::find($created->accessToken->id);
        $token->update(['tenant_id' => $tenant->id]);

        return [$tenant, $user, $created->plainTextToken];
    }
}
