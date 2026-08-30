<?php

namespace App\Http\Resources\Api\v1\Public;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicSimpleCatalogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Model $catalogItem */
        $catalogItem = $this->resource;

        return [
            'id' => $catalogItem->getAttribute('id'),
            'code' => $catalogItem->getAttribute('code'),
            'name' => $catalogItem->getAttribute('name'),
            'description' => $catalogItem->getAttribute('description'),
            'meta' => $catalogItem->getAttribute('meta') ?? [],
        ];
    }
}
