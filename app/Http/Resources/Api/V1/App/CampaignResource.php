<?php

namespace App\Http\Resources\Api\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'code' => $this->resource->code,
            'name' => $this->resource->name,
            'category' => $this->resource->category,
            'goal_amount' => $this->resource->goal_amount,
            'raised_amount' => $this->resource->raised_amount,
            'status' => $this->resource->status,
            'is_public' => $this->resource->is_public,
            'description' => $this->resource->description,
        ];
    }
}
