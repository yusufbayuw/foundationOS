<?php

namespace Modules\Donation\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Donation\Models\Donor;

class DonorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Donor $donor */
        $donor = $this->resource;

        return [
            'id' => $donor->id,
            'user_id' => $donor->user_id,
            'name' => $donor->name,
            'email' => $donor->email,
            'phone' => $donor->phone,
            'is_anonymous' => $donor->is_anonymous,
            'created_at' => $donor->created_at?->toIso8601String(),
        ];
    }
}
