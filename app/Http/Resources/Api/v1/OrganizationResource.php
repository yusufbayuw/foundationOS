<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'type' => $this->type,
            'level' => $this->level,
            'npsn' => $this->npsn,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'address' => $this->address,
            'is_main' => $this->is_main,
            'is_active' => $this->is_active,
            'established_date' => $this->established_date?->toDateString(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
