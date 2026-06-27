<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Models\Tenant;

class TenantResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Tenant $tenant */
        $tenant = $this->resource;

        return [
            'id' => $tenant->id,
            'uuid' => $tenant->uuid,
            'code' => $tenant->code,
            'name' => $tenant->name,
            'created_at' => $tenant->created_at?->toIso8601String(),
        ];
    }
}
