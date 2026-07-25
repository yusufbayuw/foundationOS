<?php

namespace App\Http\Resources\Api\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherClaimResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id'=>$this->resource->id,'voucher_id'=>$this->resource->voucher_id,'user_id'=>$this->resource->user_id,'status'=>$this->resource->status,'redeemed_at'=>$this->resource->redeemed_at?->toIso8601String()];
    }
}
