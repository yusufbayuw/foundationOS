<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\OrganizationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Models\Organization;

class OrganizationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Organization::query()->orderBy('name');

        $this->applyFilters(
            $query, $request,
            searchFields: ['name', 'code', 'short_name'],
            booleanFields: ['is_active', 'is_main'],
            exactFields: ['type', 'level'],
        );

        return $this->collectionResponse($query, $request, OrganizationResource::class);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);

        return $this->success(new OrganizationResource($organization));
    }
}
