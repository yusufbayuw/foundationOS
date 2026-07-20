<?php

namespace Modules\Voucher\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherClaimResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'voucher_id' => $this->voucher_id,
            'user_id' => $this->user_id,
            'claim_code' => $this->claim_code,
            'status' => $this->status,
            'claimed_at' => $this->claimed_at?->toISOString(),
            'used_at' => $this->used_at?->toISOString(),
        ];
    }
}
