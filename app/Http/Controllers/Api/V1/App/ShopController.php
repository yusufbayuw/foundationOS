<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Resources\Api\V1\App\OrderResource;
use App\Http\Resources\Api\V1\App\ProductResource;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Marketplace\Models\MarketplaceProduct;
use Modules\MerchOrder\Models\MerchOrder;

class ShopController extends ApiController
{
    public function products(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MarketplaceProduct::class);

        return $this->collectionResponse(MarketplaceProduct::query()->latest('id'), $request, ProductResource::class);
    }

    public function product(MarketplaceProduct $product): JsonResponse
    {
        $this->authorize('view', $product);

        return $this->success(new ProductResource($product));
    }

    public function addCartItem(Request $request): JsonResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer'], 'quantity' => ['required', 'integer', 'min:1']]);
        $cart = Cache::get($this->cartKey($request), []);
        $cart[] = $data;
        Cache::put($this->cartKey($request), $cart, now()->addDays(7));

        return $this->success(['items' => $cart], 201);
    }

    public function checkout(Request $request): JsonResponse
    {
        $this->authorize('create', MerchOrder::class);

        $order = MerchOrder::create([
            'tenant_id' => app(CurrentTenant::class)->id(),
            'code' => 'ORD-'.Str::upper(Str::random(10)),
            'name' => 'App checkout '.$request->user()->id,
            'status' => 'pending',
            'meta' => ['items' => Cache::pull($this->cartKey($request), [])],
        ]);

        return $this->success(new OrderResource($order), 201);
    }

    public function orders(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MerchOrder::class);

        return $this->collectionResponse(MerchOrder::query()->latest('id'), $request, OrderResource::class);
    }

    private function cartKey(Request $request): string
    {
        return 'app-cart:'.$request->user()->id;
    }
}
