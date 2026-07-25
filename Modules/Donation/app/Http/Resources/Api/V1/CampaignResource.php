<?php

namespace Modules\Donation\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Donation\Models\Campaign;

class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Campaign $campaign */
        $campaign = $this->resource;
        $goalAmount = (float) $campaign->goal_amount;
        $raisedAmount = (float) $campaign->raised_amount;

        return [
            'id' => $campaign->id,
            'code' => $campaign->code,
            'name' => $campaign->name,
            'category' => $campaign->category,
            'goal_amount' => $goalAmount,
            'raised_amount' => $raisedAmount,
            'progress_percentage' => $goalAmount > 0 ? round(($raisedAmount / $goalAmount) * 100, 2) : 0.0,
            'status' => $campaign->status,
            'is_public' => $campaign->is_public,
            'description' => $campaign->description,
            'created_at' => $campaign->created_at?->toIso8601String(),
        ];
    }
}
