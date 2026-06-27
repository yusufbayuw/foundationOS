<?php

namespace App\Http\Resources\Api\v1;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Models\Organization;

class OrganizationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Organization $organization */
        $organization = $this->resource;
        $establishedDate = $organization->getAttribute('established_date');
        $createdAt = $organization->getAttribute('created_at');

        return [
            'id' => $organization->getKey(),
            'code' => $organization->getAttribute('code'),
            'name' => $organization->getAttribute('name'),
            'short_name' => $organization->getAttribute('short_name'),
            'type' => $organization->getAttribute('type'),
            'level' => $organization->getAttribute('level'),
            'npsn' => $organization->getAttribute('npsn'),
            'phone' => $organization->getAttribute('phone'),
            'email' => $organization->getAttribute('email'),
            'website' => $organization->getAttribute('website'),
            'address' => $organization->getAttribute('address'),
            'is_main' => $organization->getAttribute('is_main'),
            'is_active' => $organization->getAttribute('is_active'),
            'established_date' => $establishedDate instanceof DateTimeInterface ? $establishedDate->format('Y-m-d') : null,
            'created_at' => $createdAt instanceof DateTimeInterface ? $createdAt->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
