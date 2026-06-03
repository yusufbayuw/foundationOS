<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class OpenApiController extends Controller
{
    public function v1(): JsonResponse
    {
        return response()->json($this->build('v1'));
    }

    public function v2(): JsonResponse
    {
        return response()->json($this->build('v2'));
    }

    /**
     * @return array<string, mixed>
     */
    private function build(string $version): array
    {
        $prefix = "/api/{$version}";

        return [
            'openapi' => '3.1.0',
            'info' => [
                'title' => 'FoundationOS Public API',
                'version' => $version,
            ],
            'components' => [
                'securitySchemes' => [
                    'sanctum' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                    ],
                ],
                'schemas' => [
                    'ApiMeta' => [
                        'type' => 'object',
                        'properties' => [
                            'api_version' => ['type' => 'string', 'example' => $version],
                            'per_page' => ['type' => 'integer'],
                            'has_more' => ['type' => 'boolean'],
                        ],
                    ],
                ],
            ],
            'paths' => $this->pathsFor($prefix, $version),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function pathsFor(string $prefix, string $version): array
    {
        $paths = [
            "{$prefix}/me" => [
                'get' => [
                    'summary' => 'Current authenticated user',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'User profile',
                            'content' => $this->jsonResponseWithMeta($version),
                        ],
                    ],
                ],
            ],
            "{$prefix}/tenants/current" => [
                'get' => [
                    'summary' => 'Current tenant for the API token',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Tenant details',
                            'content' => $this->jsonResponseWithMeta($version),
                        ],
                    ],
                ],
            ],
            "{$prefix}/organizations" => [
                'get' => [
                    'summary' => 'List organizations for the current tenant',
                    'security' => [
                        ['sanctum' => ['organizations:read']],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Organization collection',
                            'content' => $this->jsonResponseWithMeta($version),
                        ],
                    ],
                ],
            ],
            "{$prefix}/organizations/{id}" => [
                'get' => [
                    'summary' => 'Show organization',
                    'security' => [
                        ['sanctum' => ['organizations:read']],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Organization',
                            'content' => $this->jsonResponseWithMeta($version),
                        ],
                    ],
                ],
            ],
            "{$prefix}/students" => [
                'get' => [
                    'summary' => 'List students',
                    'security' => [['sanctum' => ['students:read']]],
                    'responses' => ['200' => ['description' => 'Student collection']],
                ],
            ],
            "{$prefix}/students/{id}" => [
                'get' => [
                    'summary' => 'Show student',
                    'security' => [['sanctum' => ['students:read']]],
                    'responses' => ['200' => ['description' => 'Student']],
                ],
            ],
            "{$prefix}/students/{id}/dashboard" => [
                'get' => [
                    'summary' => 'Student mobile dashboard',
                    'security' => [['sanctum' => ['students:read']]],
                    'responses' => ['200' => ['description' => 'Dashboard payload']],
                ],
            ],
            "{$prefix}/college-students" => [
                'get' => [
                    'summary' => 'List college students',
                    'security' => [['sanctum' => ['college_students:read']]],
                    'responses' => ['200' => ['description' => 'College student collection']],
                ],
            ],
            "{$prefix}/college-students/{id}" => [
                'get' => [
                    'summary' => 'Show college student',
                    'security' => [['sanctum' => ['college_students:read']]],
                    'responses' => ['200' => ['description' => 'College student']],
                ],
            ],
            "{$prefix}/classes" => [
                'get' => [
                    'summary' => 'List school classes',
                    'security' => [['sanctum' => ['classes:read']]],
                    'responses' => ['200' => ['description' => 'Class collection']],
                ],
            ],
            "{$prefix}/classes/{id}" => [
                'get' => [
                    'summary' => 'Show school class',
                    'security' => [['sanctum' => ['classes:read']]],
                    'responses' => ['200' => ['description' => 'School class']],
                ],
            ],
            "{$prefix}/courses" => [
                'get' => [
                    'summary' => 'List courses',
                    'security' => [['sanctum' => ['courses:read']]],
                    'responses' => ['200' => ['description' => 'Course collection']],
                ],
            ],
            "{$prefix}/courses/{id}" => [
                'get' => [
                    'summary' => 'Show course',
                    'security' => [['sanctum' => ['courses:read']]],
                    'responses' => ['200' => ['description' => 'Course']],
                ],
            ],
            "{$prefix}/employees" => [
                'get' => [
                    'summary' => 'List employees',
                    'security' => [['sanctum' => ['employees:read']]],
                    'responses' => ['200' => ['description' => 'Employee collection']],
                ],
            ],
            "{$prefix}/employees/{id}" => [
                'get' => [
                    'summary' => 'Show employee',
                    'security' => [['sanctum' => ['employees:read']]],
                    'responses' => ['200' => ['description' => 'Employee']],
                ],
            ],
            "{$prefix}/applicants" => [
                'post' => [
                    'summary' => 'Create applicant (idempotency key supported)',
                    'security' => [['sanctum' => ['applicants:write']]],
                    'responses' => ['201' => ['description' => 'Applicant created']],
                ],
            ],
            "{$prefix}/payments" => [
                'post' => [
                    'summary' => 'Record payment (idempotency key supported)',
                    'security' => [['sanctum' => ['payments:write']]],
                    'responses' => ['201' => ['description' => 'Payment recorded']],
                ],
            ],
            "{$prefix}/leave-requests" => [
                'post' => [
                    'summary' => 'Create leave request (idempotency key supported)',
                    'security' => [['sanctum' => ['leave_requests:write']]],
                    'responses' => ['201' => ['description' => 'Leave request created']],
                ],
            ],
            "{$prefix}/devices" => [
                'post' => [
                    'summary' => 'Register mobile device token',
                    'security' => [['sanctum' => []]],
                    'responses' => ['201' => ['description' => 'Device registered']],
                ],
            ],
            "{$prefix}/devices/{token}" => [
                'delete' => [
                    'summary' => 'Unregister mobile device token',
                    'security' => [['sanctum' => []]],
                    'responses' => ['204' => ['description' => 'Device removed']],
                ],
            ],
        ];

        if ($version === 'v2') {
            $paths['/api/v2'] = [
                'get' => [
                    'summary' => 'API v2 version and policy',
                    'responses' => ['200' => ['description' => 'Version metadata']],
                ],
            ];
        }

        return $paths;
    }

    /**
     * @return array<string, mixed>
     */
    private function jsonResponseWithMeta(string $version): array
    {
        return [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'data' => ['type' => 'object'],
                        'meta' => [
                            '$ref' => '#/components/schemas/ApiMeta',
                            'example' => ['api_version' => $version],
                        ],
                    ],
                ],
            ],
        ];
    }
}
