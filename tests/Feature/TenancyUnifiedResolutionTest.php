<?php

namespace Tests\Feature;

use App\Http\Middleware\BindTenantToContainer;
use App\Http\Middleware\ResolveApiTenant;
use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Core\Exceptions\MissingTenantContextException;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Procurement\Models\Vendor;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class TenancyUnifiedResolutionTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        config(['tenancy.scope_fail_closed' => false]);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_current_tenant_does_not_resolve_from_filament_directly(): void
    {
        $source = file_get_contents(base_path('Modules/Core/app/Support/Tenancy/CurrentTenant.php'));

        $this->assertStringNotContainsString('Filament::getTenant', $source);
        $this->assertStringNotContainsString('resolveFromFilament', $source);
    }

    public function test_only_bind_tenant_middleware_reads_filament_panel_tenant(): void
    {
        $matches = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path()));

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();

            if (str_contains($path, '/vendor/') || str_contains($path, '/storage/')) {
                continue;
            }

            $contents = file_get_contents($path);

            if (str_contains($path, '/tests/')) {
                continue;
            }

            if (str_contains($contents, 'Filament::getTenant()')) {
                $matches[] = str_replace(base_path().'/', '', $path);
            }
        }

        $this->assertSame(['app/Http/Middleware/BindTenantToContainer.php'], $matches);
    }

    public function test_http_queries_fail_closed_without_tenant_even_when_config_disabled(): void
    {
        config(['tenancy.scope_fail_closed' => false]);
        app(CurrentTenant::class)->forget();

        Route::middleware('web')->get('/__testing/tenancy-http-fail-closed', function () {
            return response()->json(['count' => Vendor::query()->count()]);
        });

        $this->makeTenantContext(['core', 'procurement']);

        $this->withoutExceptionHandling();
        $this->expectException(MissingTenantContextException::class);

        $this->get('/__testing/tenancy-http-fail-closed');
    }

    public function test_artisan_queries_remain_fail_open_without_tenant_context(): void
    {
        config(['tenancy.scope_fail_closed' => false]);
        app(CurrentTenant::class)->forget();

        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'procurement']);

        Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'CLI-OPEN',
            'name' => 'CLI Vendor',
            'status' => 'active',
        ]);

        $this->assertSame(1, Vendor::query()->count());
    }

    public function test_resolve_api_tenant_middleware_binds_current_tenant_from_token(): void
    {
        $tenant = Tenant::factory()->create(['code' => 'api-bind']);
        $user = User::factory()->create();
        $token = $user->createToken('bind-test');

        PersonalAccessToken::query()
            ->whereKey($token->accessToken->getKey())
            ->update(['tenant_id' => $tenant->getKey()]);

        $request = Request::create('/api/v1/me', 'GET');
        $request->setUserResolver(fn () => $user->withAccessToken(
            PersonalAccessToken::query()->findOrFail($token->accessToken->getKey())
        ));

        app()->instance(CurrentTenant::class, new CurrentTenant);

        $middleware = app(ResolveApiTenant::class);
        $middleware->handle($request, fn () => response('ok'));

        $this->assertTrue(app(CurrentTenant::class)->isBound());
        $this->assertSame($tenant->getKey(), app(CurrentTenant::class)->id());
    }

    public function test_current_tenant_required_id_throws_when_unbound(): void
    {
        app(CurrentTenant::class)->forget();

        $this->expectException(MissingTenantContextException::class);

        app(CurrentTenant::class)->requiredId();
    }

    public function test_current_tenant_model_returns_bound_tenant_instance(): void
    {
        $tenant = Tenant::factory()->create(['code' => 'model-bind']);

        app(CurrentTenant::class)->set($tenant);

        $this->assertTrue(app(CurrentTenant::class)->isBound());
        $this->assertSame($tenant->getKey(), app(CurrentTenant::class)->model()?->getKey());
    }

    public function test_bind_tenant_middleware_forgets_context_when_panel_has_no_tenant(): void
    {
        app(CurrentTenant::class)->set(Tenant::factory()->create());

        $middleware = app(BindTenantToContainer::class);
        $request = Request::create('/admin', 'GET');

        $middleware->handle($request, fn () => response('ok'));

        $this->assertFalse(app(CurrentTenant::class)->isBound());
    }
}
