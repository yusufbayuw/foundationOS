<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\Http\Controllers\Api\v1\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Marketplace\Models\MarketplaceOrder;
use Modules\MerchOrder\Models\MerchOrder;

class MyOrderController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $marketplaceOrders = MarketplaceOrder::query()
            ->where('user_id', $user->getKey())
            ->latest('id')
            ->get()
            ->map(fn (MarketplaceOrder $order): array => $this->serializeOrder('marketplace', $order));

        $merchOrders = MerchOrder::query()
            ->where('user_id', $user->getKey())
            ->latest('id')
            ->get()
            ->map(fn (MerchOrder $order): array => $this->serializeOrder('merch', $order));

        return $this->success($marketplaceOrders->concat($merchOrders)->values()->all());
    }

    /**
     * @return array{id: int, type: string, code: string|null, name: string|null, status: string, status_label: string, rejection_reason: string|null, created_at: string|null}
     */
    private function serializeOrder(string $type, MarketplaceOrder|MerchOrder $order): array
    {
        return [
            'id' => $order->id,
            'type' => $type,
            'code' => $order->code,
            'name' => $order->name,
            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'rejection_reason' => $order->rejection_reason,
            'created_at' => $order->created_at?->toISOString(),
        ];
    }
}
