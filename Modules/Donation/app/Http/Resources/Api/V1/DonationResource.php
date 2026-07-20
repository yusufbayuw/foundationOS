<?php

namespace Modules\Donation\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Donation\Models\Donation;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Donation $donation */
        $donation = $this->resource;

        return [
            'id' => $donation->id,
            'donation_number' => $donation->donation_number,
            'amount' => (float) $donation->amount,
            'payment_status' => $donation->payment_status,
            'payment_reference' => $donation->payment_reference,
            'paid_at' => $donation->paid_at?->toIso8601String(),
            'certificate_token' => $donation->certificate_token,
            'campaign' => new CampaignResource($this->whenLoaded('campaign')),
            'donor' => new DonorResource($this->whenLoaded('donor')),
            'created_at' => $donation->created_at?->toIso8601String(),
        ];
    }
}
