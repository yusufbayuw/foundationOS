<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Resources\Api\v1\EventResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Event\Models\Event;

class EventController extends ApiController
{
    private const ALLOWED_INCLUDES = ['organization'];

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Event::class);

        $query = Event::query()
            ->with($this->resolveIncludes($request, self::ALLOWED_INCLUDES))
            ->orderBy('id');

        $this->applyFilters(
            $query,
            $request,
            searchFields: ['name', 'code'],
            exactFields: ['status', 'organization_id'],
        );

        $this->applyAppFilters($query, $request);

        return $this->collectionResponse($query, $request, EventResource::class);
    }

    public function show(Request $request, Event $event): JsonResponse
    {
        $this->authorize('view', $event);

        $event->load($this->resolveIncludes($request, self::ALLOWED_INCLUDES));

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
