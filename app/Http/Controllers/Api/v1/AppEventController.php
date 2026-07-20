<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\EventResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Event\Models\Event;

class AppEventController extends ApiController
{
    private const ALLOWED_INCLUDES = ['organization'];

    public function index(Request $request): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);

        $query = Event::withoutTenantScope()
            ->with($includes)
            ->orderBy('id');

        $this->applyFilters(
            $query,
            $request,
            searchFields: ['name', 'code'],
            booleanFields: [],
            exactFields: ['status', 'organization_id'],
        );

        $this->applyAppFilters($query, $request);

        return $this->collectionResponse($query, $request, EventResource::class);
    }

    public function show(Request $request, int $event): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);
        $event = Event::withoutTenantScope()
            ->with($includes)
            ->findOrFail($event);

        return $this->success(new EventResource($event));
    }

    private function applyAppFilters(Builder $query, Request $request): void
    {
        $filters = $request->query('filter', []);

        if (! is_array($filters)) {
            return;
        }

        if (array_key_exists('active', $filters) && filter_var($filters['active'], FILTER_VALIDATE_BOOLEAN)) {
            $query->where('status', 'active');
        }

        if (array_key_exists('upcoming', $filters) && filter_var($filters['upcoming'], FILTER_VALIDATE_BOOLEAN)) {
            $query->where('meta->start_at', '>=', now()->toIso8601String());
        }
    }
}
