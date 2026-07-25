<?php

namespace App\Http\Resources\Api\v1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicCampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'category' => $this->category,
            'goal_amount' => $this->goal_amount,
            'raised_amount' => $this->raised_amount,
            'description' => $this->description,
        ];
    }
}
