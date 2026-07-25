<?php

namespace App\Http\Resources\Api\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'campaign_id' => $this->resource->campaign_id,
            'donation_number' => $this->resource->donation_number,
            'amount' => $this->resource->amount,
            'payment_status' => $this->resource->payment_status,
            'payment_reference' => $this->resource->payment_reference,
            'paid_at' => $this->resource->paid_at?->toIso8601String(),
        ];
    }
}
