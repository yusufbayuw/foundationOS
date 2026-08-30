<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ShopOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Marketplace\Models\MarketplaceOrder;
use Modules\Marketplace\Models\MarketplaceOrderItem;
use Modules\Marketplace\Models\MarketplaceProduct;
use Modules\Marketplace\Models\MarketplaceProductVariant;
use Modules\MerchOrder\Models\MerchOrder;
use Modules\MerchOrder\Models\MerchOrderItem;

class ShopCartController extends Controller
{
    public function __construct(private readonly CurrentTenant $currentTenant) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->cartPayload($this->activeCart($request))]);
    }

    public function storeItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'integer', Rule::requiredIf(fn (): bool => ! $request->filled('variant_id'))],
            'variant_id' => ['nullable', 'integer', Rule::requiredIf(fn (): bool => ! $request->filled('product_id'))],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        [$product, $variant] = $this->resolvePurchasable($data);
        $cart = $this->activeCart($request);
        $item = $cart->items()
            ->where('product_id', $product?->getKey())
            ->where('variant_id', $variant?->getKey())
            ->first();

        if ($item instanceof CartItem) {
            $item->update([
                'quantity' => $item->quantity + (int) $data['quantity'],
                'unit_price' => $this->priceFor($product, $variant),
            ]);
        } else {
            $item = $cart->items()->create([
                'product_id' => $product?->getKey(),
                'variant_id' => $variant?->getKey(),
                'quantity' => (int) $data['quantity'],
                'unit_price' => $this->priceFor($product, $variant),
            ]);
        }

        return response()->json(['data' => $this->itemPayload($item->fresh())], 201);
    }

    public function updateItem(Request $request, CartItem $item): JsonResponse
    {
        $this->abortUnlessOwnCartItem($request, $item);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item->update(['quantity' => (int) $data['quantity']]);

        return response()->json(['data' => $this->itemPayload($item->fresh())]);
    }

    public function destroyItem(Request $request, CartItem $item): JsonResponse
    {
        $this->abortUnlessOwnCartItem($request, $item);
        $item->delete();

        return response()->json(status: 204);
    }

    public function checkout(Request $request): JsonResponse
    {
        $cart = $this->activeCart($request)->load('items.product', 'items.variant');

        if ($cart->items->isEmpty()) {
            return response()->json(['error' => ['code' => 'empty_cart', 'message' => 'Cart is empty.']], 422);
        }

        $orders = DB::transaction(function () use ($cart): array {
            $orders = [];

            foreach ($cart->items->groupBy(fn (CartItem $item): string => $this->sourceFor($item)) as $source => $items) {
                $order = $source === 'merch'
                    ? MerchOrder::query()->create($this->orderAttributes($cart, $items, ShopOrderStatus::PendingPayment))
                    : MarketplaceOrder::query()->create($this->orderAttributes($cart, $items, ShopOrderStatus::PendingPayment));

                foreach ($items as $item) {
                    $model = $source === 'merch' ? MerchOrderItem::class : MarketplaceOrderItem::class;
                    $model::query()->create($this->orderItemAttributes($cart, $order->getKey(), $item));
                }

                $orders[] = ['type' => $source, 'id' => $order->getKey(), 'total' => $this->sumItems($items)];
            }

            $cart->update(['status' => 'checked_out']);

            return $orders;
        });

        return response()->json(['data' => ['orders' => $orders]], 201);
    }

    private function activeCart(Request $request): Cart
    {
        return Cart::query()->firstOrCreate([
            'user_id' => $request->user()->getKey(),
            'tenant_id' => $this->currentTenant->id(),
            'status' => 'active',
        ]);
    }

    /** @param array{product_id?: int|null, variant_id?: int|null} $data */
    private function resolvePurchasable(array $data): array
    {
        $tenantId = $this->currentTenant->id();
        $product = isset($data['product_id']) ? MarketplaceProduct::query()->whereKey($data['product_id'])->where('tenant_id', $tenantId)->where('status', 'active')->firstOrFail() : null;
        $variant = isset($data['variant_id']) ? MarketplaceProductVariant::query()->whereKey($data['variant_id'])->where('tenant_id', $tenantId)->where('status', 'active')->firstOrFail() : null;

        return [$product, $variant];
    }

    private function priceFor(?MarketplaceProduct $product, ?MarketplaceProductVariant $variant): float
    {
        $meta = $variant?->meta ?? $product?->meta ?? [];

        return (float) ($meta['price'] ?? 0);
    }

    private function sourceFor(CartItem $item): string
    {
        $meta = $item->variant?->meta ?? $item->product?->meta ?? [];

        return in_array($meta['source'] ?? 'marketplace', ['merch', 'merchorder'], true) ? 'merch' : 'marketplace';
    }

    private function abortUnlessOwnCartItem(Request $request, CartItem $item): void
    {
        $cart = Cart::withoutTenantScope()->find($item->cart_id);

        if (! $cart instanceof Cart || (int) $cart->user_id !== (int) $request->user()->getKey() || (int) $cart->tenant_id !== (int) $this->currentTenant->id() || $cart->status !== 'active') {
            abort(404);
        }
    }

    private function cartPayload(Cart $cart): array
    {
        $cart->load('items.product', 'items.variant');

        return [
            'id' => $cart->getKey(),
            'status' => $cart->status,
            'items' => $cart->items->map(fn (CartItem $item): array => $this->itemPayload($item))->values(),
            'subtotal' => $this->sumItems($cart->items),
            'total' => $this->sumItems($cart->items),
        ];
    }

    private function itemPayload(CartItem $item): array
    {
        return [
            'id' => $item->getKey(),
            'product_id' => $item->product_id,
            'variant_id' => $item->variant_id,
            'quantity' => $item->quantity,
            'unit_price' => (float) $item->unit_price,
            'line_total' => (float) $item->unit_price * $item->quantity,
        ];
    }

    private function orderAttributes(Cart $cart, mixed $items, ShopOrderStatus $status): array
    {
        return [
            'tenant_id' => $cart->tenant_id,
            'status' => $status,
            'name' => 'Cart #'.$cart->getKey(),
            'meta' => [
                'cart_id' => $cart->getKey(),
                'user_id' => $cart->user_id,
                'subtotal' => $this->sumItems($items),
                'total' => $this->sumItems($items),
            ],
        ];
    }

    private function orderItemAttributes(Cart $cart, int $orderId, CartItem $item): array
    {
        $lineTotal = (float) $item->unit_price * $item->quantity;

        return [
            'tenant_id' => $cart->tenant_id,
            'status' => 'active',
            'name' => $item->product?->name ?? $item->variant?->name,
            'meta' => [
                'order_id' => $orderId,
                'cart_item_id' => $item->getKey(),
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => $lineTotal,
            ],
        ];
    }

    private function sumItems(mixed $items): float
    {
        return (float) collect($items)->sum(fn (CartItem $item): float => (float) $item->unit_price * $item->quantity);
    }
}
