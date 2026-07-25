<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Resources\Api\V1\App\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        return $this->collectionResponse($request->user()->notifications()->latest(), $request, NotificationResource::class);
    }
}
