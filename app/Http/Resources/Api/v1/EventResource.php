<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Event\Models\Event;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Event $event */
        $event = $this->resource;
        $meta = $event->meta ?? [];

        return [
            'id' => $event->id,
            'code' => $event->code,
            'name' => $event->name,
            'status' => $event->status,
            'description' => $event->description,
            'start_at' => $meta['start_at'] ?? null,
            'end_at' => $meta['end_at'] ?? null,
            'location' => $meta['location'] ?? null,
            'dresscode' => $meta['dresscode'] ?? null,
            'registration_url' => $meta['registration_url'] ?? null,
            'cover_image' => $meta['cover_image'] ?? null,
            'organization_id' => $event->organization_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($event->organization)),
            'created_at' => $event->created_at?->toIso8601String(),
        ];
    }
}
