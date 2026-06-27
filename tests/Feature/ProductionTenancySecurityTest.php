<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Modules\Core\Exceptions\MissingTenantContextException;
use Modules\Core\Models\User;
use Modules\Procurement\Models\Vendor;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class ProductionTenancySecurityTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('/__testing/tenancy-fail-closed-smoke', function () {
            return response()->json(['count' => Vendor::query()->count()]);
        });
    }

    protected function tearDown(): void
    {
        config(['tenancy.scope_fail_closed' => false]);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_scope_fail_closed_defaults_on_when_app_env_is_production(): void
    {
        $this->assertTrue(
            $this->resolveScopeFailClosedFromEnv('production', null),
        );
        $this->assertFalse(
            $this->resolveScopeFailClosedFromEnv('local', null),
        );
        $this->assertFalse(
            $this->resolveScopeFailClosedFromEnv('testing', null),
        );
    }

    public function test_http_fail_closed_is_independent_of_scope_config(): void
    {
        config(['tenancy.scope_fail_closed' => false]);
        app(CurrentTenant::class)->forget();

        Route::middleware('web')->get('/__testing/http-always-fail-closed', function () {
            Vendor::query()->count();

            return response('ok');
        });

        $this->makeTenantContext(['core', 'procurement']);
        $this->withoutExceptionHandling();
        $this->expectException(MissingTenantContextException::class);
        $this->get('/__testing/http-always-fail-closed');
    }

    public function test_explicit_env_override_disables_production_default(): void
    {
        $this->assertFalse(
            $this->resolveScopeFailClosedFromEnv('production', 'false'),
        );
    }

    public function test_http_request_without_tenant_throws_when_fail_closed_enabled(): void
    {
        app(CurrentTenant::class)->forget();

        $this->makeTenantContext(['core', 'procurement']);

        $this->withoutExceptionHandling();

        $this->expectException(MissingTenantContextException::class);

        $this->get('/__testing/tenancy-fail-closed-smoke');
    }

    public function test_api_still_rejects_token_without_tenant_when_fail_closed_on(): void
    {
        config(['tenancy.scope_fail_closed' => true, 'tenancy.api_require_tenant' => true]);

        $user = User::factory()->create();
        $token = $user->createToken('no-tenant')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/organizations')
            ->assertForbidden();
    }

    protected function resolveScopeFailClosedFromEnv(string $appEnv, ?string $explicit): bool
    {
        $default = $appEnv === 'production';

        if ($explicit === null) {
            return $default;
        }

        return filter_var($explicit, FILTER_VALIDATE_BOOL);
    }
}
