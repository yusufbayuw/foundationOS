<?php

namespace Tests\Feature;

use App\Enums\ShopOrderStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PersonalAccessToken;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Marketplace\Models\MarketplaceOrder;
use Modules\Marketplace\Models\MarketplaceProduct;
use Tests\TestCase;

class ShopCartApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::query()->create([
            'code' => 'shop-cart-plan',
            'name' => 'Shop Cart Plan',
            'included_modules' => ['core', 'marketplace', 'merchorder'],
        ]);

        $this->user = User::query()->create([
            'name' => 'Cart User',
            'email' => 'cart-user@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'shop-cart-tenant',
            'name' => 'Shop Cart Tenant',
            'subscription_plan_id' => $plan->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $newToken = $this->user->createToken('cart-token');
        PersonalAccessToken::query()
            ->whereKey($newToken->accessToken->getKey())
            ->update(['tenant_id' => $this->tenant->getKey()]);

        $this->token = $newToken->plainTextToken;
    }

    public function test_user_can_add_update_and_remove_cart_item(): void
    {
        $product = $this->product(['price' => 15000]);

        $createResponse = $this->withToken($this->token)->postJson('/api/v1/app/shop/cart/items', [
            'product_id' => $product->getKey(),
            'quantity' => 2,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.quantity', 2)
            ->assertJsonPath('data.unit_price', 15000);

        $itemId = $createResponse->json('data.id');

        $this->withToken($this->token)->patchJson("/api/v1/app/shop/cart/items/{$itemId}", [
            'quantity' => 5,
        ])->assertOk()
            ->assertJsonPath('data.quantity', 5)
            ->assertJsonPath('data.line_total', 75000);

        $this->withToken($this->token)->deleteJson("/api/v1/app/shop/cart/items/{$itemId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cart_items', ['id' => $itemId]);
    }

    public function test_checkout_empty_cart_is_rejected(): void
    {
        $this->withToken($this->token)->postJson('/api/v1/app/shop/checkout')
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'empty_cart');
    }

    public function test_cart_and_checkout_recalculate_price_server_side(): void
    {
        $product = $this->product(['price' => 22500]);

        $this->withToken($this->token)->postJson('/api/v1/app/shop/cart/items', [
            'product_id' => $product->getKey(),
            'quantity' => 3,
            'unit_price' => 1,
        ])->assertCreated()
            ->assertJsonPath('data.unit_price', 22500)
            ->assertJsonPath('data.line_total', 67500);

        $this->withToken($this->token)->postJson('/api/v1/app/shop/checkout')
            ->assertCreated()
            ->assertJsonPath('data.orders.0.total', 67500);

        $order = MarketplaceOrder::withoutTenantScope()->firstOrFail();

        $this->assertSame(67500, (int) $order->meta['total']);
        $this->assertSame(ShopOrderStatus::PendingPayment, $order->status);
    }

    public function test_cart_items_are_isolated_by_tenant(): void
    {
        $otherTenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'other-shop-cart-tenant',
            'name' => 'Other Shop Cart Tenant',
            'subscription_plan_id' => $this->tenant->subscription_plan_id,
            'created_by' => $this->user->getKey(),
        ]);
        $otherProduct = MarketplaceProduct::withoutTenantScope()->create([
            'tenant_id' => $otherTenant->getKey(),
            'name' => 'Other Product',
            'status' => 'active',
            'meta' => ['price' => 99999],
        ]);

        $this->withToken($this->token)->postJson('/api/v1/app/shop/cart/items', [
            'product_id' => $otherProduct->getKey(),
            'quantity' => 1,
        ])->assertNotFound();

        $cart = Cart::withoutTenantScope()->create([
            'user_id' => $this->user->getKey(),
            'tenant_id' => $otherTenant->getKey(),
            'status' => 'active',
        ]);
        $item = CartItem::query()->create([
            'cart_id' => $cart->getKey(),
            'product_id' => $otherProduct->getKey(),
            'quantity' => 1,
            'unit_price' => 99999,
        ]);

        $this->withToken($this->token)->patchJson("/api/v1/app/shop/cart/items/{$item->getKey()}", [
            'quantity' => 2,
        ])->assertNotFound();
    }

    /** @param array{price: int|float, source?: string} $meta */
    private function product(array $meta): MarketplaceProduct
    {
        return MarketplaceProduct::withoutTenantScope()->create([
            'tenant_id' => $this->tenant->getKey(),
            'name' => 'Test Product',
            'status' => 'active',
            'meta' => $meta,
        ]);
    }
}
