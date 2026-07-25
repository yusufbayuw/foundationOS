<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Resources\Api\V1\App\EventResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Event\Models\Event;

class EventController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Event::class);

        return $this->collectionResponse(Event::query()->latest('id'), $request, EventResource::class);
    }

    public function show(Event $event): JsonResponse
    {
        $this->authorize('view', $event);

        return $this->success(new EventResource($event));
    }
}
