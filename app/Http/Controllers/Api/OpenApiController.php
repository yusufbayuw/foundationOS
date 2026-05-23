<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class OpenApiController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'openapi' => '3.1.0',
            'info' => [
                'title' => 'FoundationOS Public API',
                'version' => 'v1',
            ],
            'components' => [
                'securitySchemes' => [
                    'sanctum' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                    ],
                ],
            ],
            'paths' => [
                '/api/v1/organizations' => [
                    'get' => [
                        'summary' => 'List organizations for the current tenant',
                        'security' => [
                            ['sanctum' => ['organizations:read']],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Organization collection'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
