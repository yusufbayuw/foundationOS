<?php

namespace Modules\Voucher\Http\Controllers;

use App\Http\Controllers\Api\v1\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Voucher\Http\Resources\VoucherClaimResource;
use Modules\Voucher\Http\Resources\VoucherResource;
use Modules\Voucher\Models\Voucher;
use Modules\Voucher\Models\VoucherClaim;
use Modules\Voucher\Services\VoucherClaimService;
use RuntimeException;

class VoucherController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Voucher::query()
            ->where('status', 'active')
            ->orderByDesc('id');

        $this->applyFilters($query, $request, searchFields: ['title', 'code'], exactFields: ['redemption_method']);

        return $this->collectionResponse($query, $request, VoucherResource::class);
    }

    public function show(Voucher $voucher): JsonResponse
    {
        return $this->success(new VoucherResource($voucher));
    }

    public function claim(Voucher $voucher, Request $request, VoucherClaimService $service): JsonResponse
    {
        try {
            $claim = $service->claim($voucher, $request->user());
        } catch (RuntimeException $exception) {
            return $this->voucherError($exception->getMessage());
        }

        return $this->success(new VoucherClaimResource($claim), 201);
    }

    public function redeem(VoucherClaim $claim, Request $request, VoucherClaimService $service): JsonResponse
    {
        $validated = $request->validate([
            'claim_code' => ['nullable', 'string'],
        ]);

        try {
            $claim = $service->redeem($claim, $request->user(), $validated['claim_code'] ?? null);
        } catch (RuntimeException $exception) {
            return $this->voucherError($exception->getMessage());
        }

        return $this->success(new VoucherClaimResource($claim));
    }

    private function voucherError(string $code): JsonResponse
    {
        return match ($code) {
            'duplicate_claim' => $this->error($code, 'Voucher already claimed by this user.', 409),
            'quota_exhausted' => $this->error($code, 'Voucher quota has been exhausted.', 409),
            'voucher_unavailable', 'voucher_expired' => $this->error($code, 'Voucher is not available.', 422),
            'invalid_claim', 'invalid_claim_code' => $this->error($code, 'Voucher claim is invalid.', 403),
            'claim_not_redeemable' => $this->error($code, 'Voucher claim cannot be redeemed.', 409),
            default => $this->error('voucher_error', 'Voucher operation failed.', 422),
        };
    }
}
