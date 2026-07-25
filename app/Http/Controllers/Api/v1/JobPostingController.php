<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\JobPostingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Alumni\Models\JobPosting;

class JobPostingController extends ApiController
{
    private const ALLOWED_INCLUDES = ['organization'];

    public function index(Request $request): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);

        $query = JobPosting::query()
            ->active()
            ->with($includes)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $this->applyFilters(
            $query,
            $request,
            searchFields: ['name', 'role_title', 'company', 'location'],
            exactFields: ['employment_type', 'application_method', 'organization_id'],
        );

        return $this->collectionResponse($query, $request, JobPostingResource::class);
    }

    public function show(Request $request, JobPosting $jobPosting): JsonResponse
    {
        abort_if(! JobPosting::query()->active()->whereKey($jobPosting->getKey())->exists(), 404);

        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);
        $jobPosting->load($includes);

        return $this->success(new JobPostingResource($jobPosting));
    }
}
