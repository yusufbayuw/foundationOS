<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Models\Organization;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Organization $organization */
        $organization = $this->resource;

        return [
            'id' => $organization->id,
            'code' => $organization->code,
            'name' => $organization->name,
            'short_name' => $organization->short_name,
            'type' => $organization->type,
            'level' => $organization->level,
            'npsn' => $organization->npsn,
            'phone' => $organization->phone,
            'email' => $organization->email,
            'website' => $organization->website,
            'address' => $organization->address,
            'is_main' => $organization->is_main,
            'is_active' => $organization->is_active,
            'established_date' => $organization->established_date?->toDateString(),
            'created_at' => $organization->created_at?->toIso8601String(),
        ];
    }
}
