<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Resources\Api\V1\App\VoucherClaimResource;
use App\Http\Resources\Api\V1\App\VoucherResource;
use App\Models\Voucher;
use App\Models\VoucherClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Voucher::class);

        return $this->collectionResponse(Voucher::query()->where('status', 'active')->latest('id'), $request, VoucherResource::class);
    }

    public function claim(Request $request, Voucher $voucher): JsonResponse
    {
        $this->authorize('claim', $voucher);

        $claim = VoucherClaim::firstOrCreate([
            'voucher_id' => $voucher->id,
            'user_id' => $request->user()->id,
        ], [
            'tenant_id' => $voucher->tenant_id,
            'status' => 'claimed',
        ]);

        return $this->success(new VoucherClaimResource($claim), 201);
    }

    public function redeem(VoucherClaim $claim): JsonResponse
    {
        $this->authorize('redeem', $claim);

        $claim->update(['status' => 'redeemed', 'redeemed_at' => now()]);

        return $this->success(new VoucherClaimResource($claim->refresh()));
    }
}
