<?php

namespace Tests\Feature;

use App\Concerns\InteractsWithTenant;
use App\Console\Commands\Concerns\TenantProbeCommand;
use App\Queue\Middleware\WithTenantContext;
use App\Support\CurrentTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Procurement\Models\Vendor;
use Tests\TestCase;

class TenantAwareCaptureJob implements ShouldQueue
{
    use InteractsWithQueue, InteractsWithTenant, Queueable, SerializesModels;

    public static ?int $observedTenantId = null;

    public static int $observedVendorCount = 0;

    public function __construct()
    {
        $this->captureCurrentTenant();
    }

    public function handle(CurrentTenant $currentTenant): void
    {
        self::$observedTenantId = $currentTenant->id();
        self::$observedVendorCount = Vendor::count();
    }
}

class JobRestoresTenantContextTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(CurrentTenant::class)->forget();
        TenantAwareCaptureJob::$observedTenantId = null;
        TenantAwareCaptureJob::$observedVendorCount = 0;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    protected function makeTenant(string $code): Tenant
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'job-test'],
            ['name' => 'Job Test', 'included_modules' => ['core', 'procurement']],
        );

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => ucfirst($code),
            'subscription_plan_id' => $plan->id,
        ]);
    }

    public function test_job_constructor_captures_current_tenant_id(): void
    {
        $tenant = $this->makeTenant('capture');

        app(CurrentTenant::class)->set($tenant);
        $job = new TenantAwareCaptureJob;

        $this->assertSame($tenant->id, $job->tenantId);
    }

    public function test_on_tenant_overrides_captured_tenant(): void
    {
        $tenant = $this->makeTenant('override');

        $job = (new TenantAwareCaptureJob)->onTenant($tenant);

        $this->assertSame($tenant->id, $job->tenantId);
    }

    public function test_middleware_restores_tenant_context_during_handle(): void
    {
        $tenantA = $this->makeTenant('mid-a');
        $tenantB = $this->makeTenant('mid-b');

        Vendor::withoutTenantScope()->create(['tenant_id' => $tenantA->id, 'name' => 'A1']);
        Vendor::withoutTenantScope()->create(['tenant_id' => $tenantA->id, 'name' => 'A2']);
        Vendor::withoutTenantScope()->create(['tenant_id' => $tenantB->id, 'name' => 'B1']);

        $job = (new TenantAwareCaptureJob)->onTenant($tenantA);

        $middleware = $job->middleware();
        $this->assertCount(1, $middleware);
        $this->assertInstanceOf(WithTenantContext::class, $middleware[0]);

        // simulate the queue worker calling the middleware → handle pipeline
        $middleware[0]->handle($job, function ($j) {
            $j->handle(app(CurrentTenant::class));
        });

        $this->assertSame($tenantA->id, TenantAwareCaptureJob::$observedTenantId);
        $this->assertSame(2, TenantAwareCaptureJob::$observedVendorCount);
        $this->assertNull(app(CurrentTenant::class)->id(), 'tenant context should be cleared after middleware finishes');
    }

    public function test_dispatching_through_sync_queue_runs_inside_tenant_context(): void
    {
        $tenant = $this->makeTenant('sync');
        Vendor::withoutTenantScope()->create(['tenant_id' => $tenant->id, 'name' => 'one']);

        app(CurrentTenant::class)->set($tenant);

        Bus::dispatchSync(new TenantAwareCaptureJob);

        $this->assertSame($tenant->id, TenantAwareCaptureJob::$observedTenantId);
        $this->assertSame(1, TenantAwareCaptureJob::$observedVendorCount);
    }

    public function test_tenant_run_command_executes_inside_target_tenant_context(): void
    {
        $tenantA = $this->makeTenant('cli-a');
        $tenantB = $this->makeTenant('cli-b');

        Vendor::withoutTenantScope()->create(['tenant_id' => $tenantB->id, 'name' => 'only-b']);

        app(CurrentTenant::class)->set($tenantA);

        Artisan::registerCommand(new TenantProbeCommand);

        $exit = Artisan::call('tenant:run', [
            'tenant' => (string) $tenantB->id,
            'cmd' => 'tests:tenant-probe',
        ]);

        $this->assertSame(0, $exit);
        $this->assertSame($tenantB->id, TenantProbeCommand::$observedTenantId);
        $this->assertSame($tenantA->id, app(CurrentTenant::class)->id(), 'caller context should be restored');
    }
}
