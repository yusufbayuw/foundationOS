<?php

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\JsonResponse;

class VersionController extends ApiController
{
    public function show(): JsonResponse
    {
        return $this->success([
            'version' => 'v2',
            'status' => 'available',
            'policy' => 'additive_minor_breaking_major',
            'documentation' => url('/api/v2/openapi.json'),
        ]);
    }
}
