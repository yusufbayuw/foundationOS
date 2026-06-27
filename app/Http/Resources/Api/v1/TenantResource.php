<?php

namespace App\Http\Resources\Api\v1;

use DateTimeInterface;
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
        $createdAt = $tenant->getAttribute('created_at');

        return [
            'id' => $tenant->getKey(),
            'uuid' => $tenant->getAttribute('uuid'),
            'code' => $tenant->getAttribute('code'),
            'name' => $tenant->getAttribute('name'),
            'created_at' => $createdAt instanceof DateTimeInterface ? $createdAt->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
