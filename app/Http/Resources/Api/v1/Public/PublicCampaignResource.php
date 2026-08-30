<?php

namespace App\Http\Resources\Api\v1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Donation\Models\Campaign;

class PublicCampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Campaign $campaign */
        $campaign = $this->resource;

        return [
            'id' => $campaign->id,
            'code' => $campaign->code,
            'name' => $campaign->name,
            'category' => $campaign->category,
            'goal_amount' => $campaign->goal_amount,
            'raised_amount' => $campaign->raised_amount,
            'description' => $campaign->description,
        ];
    }
}
