<?php

namespace Tests\Tenant;

use App\Events\TenantSwitched;
use App\Exceptions\MissingTenantContextException;
use App\Http\Middleware\BindTenantToContainer;
use App\Queue\Middleware\WithTenantContext;
use App\Support\CurrentTenant;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Modules\Core\Models\Tenant;
use Modules\Procurement\Models\Vendor;
use RuntimeException;
use Tests\TestCase;

class TenantContextWriteSafetyTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    public function test_explicit_foreign_tenant_id_is_rejected_on_create(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        app(CurrentTenant::class)->forTenant($tenantA, function () use ($tenantB): void {
            try {
                Vendor::create(['name' => 'Forged Vendor', 'tenant_id' => $tenantB->id]);
                $this->fail('Foreign tenant ID should never be accepted under a tenant context.');
            } catch (AuthorizationException) {
                $this->assertDatabaseMissing('vendors', ['name' => 'Forged Vendor']);
            }
        });
    }

    public function test_foreign_record_cannot_be_reassigned_or_modified_even_when_scope_is_bypassed(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $vendor = Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id, 'name' => 'Tenant B Vendor',
        ]);

        app(CurrentTenant::class)->forTenant($tenantA, function () use ($vendor, $tenantA): void {
            $vendor->name = 'Hijacked';
            $vendor->tenant_id = $tenantA->id;

            $this->expectException(AuthorizationException::class);
            $vendor->save();
        });
    }

    public function test_same_tenant_writes_and_explicit_nested_context_are_allowed(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $currentTenant = app(CurrentTenant::class);

        $currentTenant->forTenant($tenantA, function () use ($tenantA, $tenantB, $currentTenant): void {
            $a = Vendor::create(['name' => 'A']);
            $this->assertEquals($tenantA->id, $a->tenant_id);

            $currentTenant->forTenant($tenantB, function () use ($tenantB): void {
                $b = Vendor::create(['name' => 'B']);
                $this->assertEquals($tenantB->id, $b->tenant_id);
            });

            $this->assertEquals($tenantA->id, $currentTenant->id());
        });

        $this->assertNull($currentTenant->id());
    }

    public function test_panel_middleware_restores_tenant_and_team_after_success_and_exception(): void
    {
        Event::fake([TenantSwitched::class]);
        $oldTenant = Tenant::factory()->create();
        $newTenant = Tenant::factory()->create();
        Filament::shouldReceive('getTenant')->andReturn($newTenant);

        $context = app(CurrentTenant::class);
        $context->set($oldTenant);
        $oldTeam = getPermissionsTeamId();

        try {
            $response = app(BindTenantToContainer::class)->handle(
                Request::create('/admin', 'GET'),
                function () use ($context, $newTenant) {
                    $this->assertEquals($newTenant->id, $context->id());
                    $this->assertEquals($newTenant->id, getPermissionsTeamId());

                    return response('ok');
                },
            );

            $this->assertEquals('ok', $response->getContent());
            $this->assertEquals($oldTenant->id, $context->id());
            $this->assertSame($oldTeam, getPermissionsTeamId());

            app(BindTenantToContainer::class)->handle(
                Request::create('/admin', 'GET'),
                function () use ($newTenant, $context) {
                    $this->assertEquals($newTenant->id, $context->id());
                    throw new RuntimeException('injected exception');
                },
            );

            $this->fail('An exception thrown by the request must propagate.');
        } catch (RuntimeException $e) {
            $this->assertSame('injected exception', $e->getMessage());
            $this->assertEquals($oldTenant->id, $context->id());
            $this->assertSame($oldTeam, getPermissionsTeamId());
        }
    }

    public function test_tenantless_panel_route_cannot_inherit_prior_tenant_or_team(): void
    {
        $priorTenant = Tenant::factory()->create();
        Filament::shouldReceive('getTenant')->andReturn(null);

        $context = app(CurrentTenant::class);
        $context->set($priorTenant);
        $oldTeam = getPermissionsTeamId();
        setPermissionsTeamId($priorTenant->id);

        try {
            app(BindTenantToContainer::class)->handle(
                Request::create('/admin/login', 'GET'),
                function () use ($context) {
                    $this->assertNull($context->id());
                    $this->assertSame(0, getPermissionsTeamId());

                    return response('ok');
                },
            );

            $this->assertEquals($priorTenant->id, $context->id());
            $this->assertEquals($priorTenant->id, getPermissionsTeamId());
        } finally {
            setPermissionsTeamId($oldTeam);
        }
    }

    public function test_tenant_job_middleware_restores_previous_state_on_failure(): void
    {
        $old = Tenant::factory()->create();
        $new = Tenant::factory()->create();
        $context = app(CurrentTenant::class);
        $context->set($old);
        $oldTeam = getPermissionsTeamId();

        try {
            (new WithTenantContext($new->id))->handle((object) [], function () use ($new, $context) {
                $this->assertEquals($new->id, $context->id());
                $this->assertEquals($new->id, getPermissionsTeamId());
                throw new RuntimeException('job failure');
            });

            $this->fail('Job failure should propagate.');
        } catch (RuntimeException $e) {
            $this->assertSame('job failure', $e->getMessage());
            $this->assertEquals($old->id, $context->id());
            $this->assertSame($oldTeam, getPermissionsTeamId());
        }
    }

    public function test_known_tenantless_system_job_uses_isolated_global_context(): void
    {
        $priorTenant = Tenant::factory()->create();
        $context = app(CurrentTenant::class);
        $oldTeam = getPermissionsTeamId();
        $context->set($priorTenant);
        setPermissionsTeamId($priorTenant->id);

        try {
            $processed = (new WithTenantContext(null))->handle(
                new \Modules\Workflow\Jobs\CheckWorkflowSlaJob(999),
                function () use ($context): bool {
                    $this->assertNull($context->id(), 'Global job must not inherit worker tenant.');
                    $this->assertSame(0, getPermissionsTeamId());

                    return true;
                },
            );

            $this->assertTrue($processed);
            $this->assertEquals($priorTenant->id, $context->id());
            $this->assertEquals($priorTenant->id, getPermissionsTeamId());
        } finally {
            $context->forget();
            setPermissionsTeamId($oldTeam);
        }
    }

    public function test_tenant_job_without_captured_tenant_fails_closed(): void
    {
        $jobWasRun = false;

        try {
            (new WithTenantContext(null))->handle((object) [], function () use (&$jobWasRun): void {
                $jobWasRun = true;
            });

            $this->fail('Tenantless jobs must be rejected.');
        } catch (MissingTenantContextException) {
            $this->assertFalse($jobWasRun);
        }
    }

    public function test_deleted_tenant_is_not_resolved_by_queue_middleware(): void
    {
        $tenant = Tenant::factory()->create();
        $tenant->delete();
        $ran = false;

        try {
            (new WithTenantContext($tenant->id))->handle((object) [], function () use (&$ran): void {
                $ran = true;
            });
            $this->fail('Deleted tenant cannot resume queued work.');
        } catch (RuntimeException) {
            $this->assertFalse($ran);
        }
    }
}
