<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Resources\Api\V1\App\JobPostingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Alumni\Models\JobPosting;

class JobController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', JobPosting::class);

        return $this->collectionResponse(JobPosting::query()->latest('id'), $request, JobPostingResource::class);
    }

    public function show(JobPosting $jobPosting): JsonResponse
    {
        $this->authorize('view', $jobPosting);

        return $this->success(new JobPostingResource($jobPosting));
    }
}
