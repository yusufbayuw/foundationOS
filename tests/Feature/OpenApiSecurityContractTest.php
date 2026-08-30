<?php

namespace Tests\Feature;

use Tests\TestCase;

class OpenApiSecurityContractTest extends TestCase
{
    public function test_v1_contract_documents_member_registration_runtime_security(): void
    {
        $response = $this->getJson('/api/openapi.json');

        $response->assertOk()
            ->assertJsonPath('components.securitySchemes.sanctum.type', 'http')
            ->assertJsonPath('components.securitySchemes.sanctum.scheme', 'bearer')
            ->assertJsonPath('paths./api/v1/members/register.post.security.0.sanctum', [])
            ->assertJsonPath('paths./api/v1/members/register.post.x-required-abilities.0', 'api:write')
            ->assertJsonPath('paths./api/v1/members/register.post.responses.403.description', 'Tenant scope or token ability is missing');
    }

    public function test_http_bearer_security_requirements_do_not_use_oauth_scope_syntax(): void
    {
        $document = $this->getJson('/api/openapi.json')->assertOk()->json();

        foreach ($document['paths'] as $path => $pathItem) {
            foreach ($pathItem as $method => $operation) {
                foreach ($operation['security'] ?? [] as $requirement) {
                    $this->assertSame(
                        [],
                        $requirement['sanctum'] ?? null,
                        "{$method} {$path} must use an empty HTTP bearer security requirement.",
                    );
                }
            }
        }
    }

    public function test_documented_write_operations_use_the_runtime_api_write_ability(): void
    {
        $document = $this->getJson('/api/openapi.json')->assertOk()->json();

        foreach ([
            '/api/v1/applicants',
            '/api/v1/payments',
            '/api/v1/leave-requests',
            '/api/v1/devices',
            '/api/v1/devices/{token}',
            '/api/v1/members/register',
        ] as $path) {
            $operation = $document['paths'][$path][array_key_first($document['paths'][$path])];

            $this->assertSame(['api:write'], $operation['x-required-abilities'] ?? null, $path);
        }
    }
}
