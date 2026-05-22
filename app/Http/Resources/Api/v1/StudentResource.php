<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nis' => $this->nis,
            'nisn' => $this->nisn,
            'status' => $this->status,
            'track' => $this->track,
            'entry_date' => $this->entry_date?->toDateString(),
            'entry_type' => $this->entry_type,
            'graduation_date' => $this->graduation_date?->toDateString(),
            'organization_id' => $this->organization_id,
            'academic_year_id' => $this->academic_year_id,
            'user_id' => $this->user_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($this->organization)),
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
