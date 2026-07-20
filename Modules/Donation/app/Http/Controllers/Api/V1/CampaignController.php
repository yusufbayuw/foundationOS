<?php

namespace Modules\Donation\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\v1\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Donation\Http\Resources\Api\V1\CampaignResource;
use Modules\Donation\Models\Campaign;

class CampaignController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Campaign::query()
            ->where('status', 'active')
            ->where('is_public', true)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $this->applyFilters($query, $request, searchFields: ['name', 'code', 'category']);

        return $this->collectionResponse($query, $request, CampaignResource::class);
    }

    public function show(int $campaign): JsonResponse
    {
        $campaign = Campaign::query()
            ->where('status', 'active')
            ->where('is_public', true)
            ->findOrFail($campaign);

        return $this->success(new CampaignResource($campaign));
    }
}
