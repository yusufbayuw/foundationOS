<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Alumni\Models\JobPosting;

class JobPostingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var JobPosting $jobPosting */
        $jobPosting = $this->resource;

        return [
            'id' => $jobPosting->id,
            'code' => $jobPosting->code,
            'title' => $jobPosting->role_title ?? $jobPosting->name,
            'name' => $jobPosting->name,
            'company' => $jobPosting->company,
            'location' => $jobPosting->location,
            'employment_type' => $jobPosting->employment_type,
            'status' => $jobPosting->status,
            'description' => $jobPosting->description,
            'application_method' => $jobPosting->application_method,
            'application_url' => $jobPosting->application_url,
            'application_email' => $jobPosting->application_email,
            'expires_at' => $jobPosting->expires_at?->toIso8601String(),
            'organization_id' => $jobPosting->organization_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($jobPosting->organization)),
            'created_at' => $jobPosting->created_at?->toIso8601String(),
        ];
    }
}
