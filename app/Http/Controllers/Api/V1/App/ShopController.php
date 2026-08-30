<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Resources\Api\V1\App\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Marketplace\Models\MarketplaceProduct;

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

}
