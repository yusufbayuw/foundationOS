<?php

namespace Modules\Voucher\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'title' => $this->title,
            'description' => $this->description,
            'code' => $this->code,
            'quota' => $this->quota,
            'claimed_count' => $this->claimed_count,
            'remaining_quota' => max(0, $this->quota - $this->claimed_count),
            'start_at' => $this->start_at?->toISOString(),
            'end_at' => $this->end_at?->toISOString(),
            'status' => $this->status,
            'terms' => $this->terms,
            'redemption_method' => $this->redemption_method,
        ];
    }
}
