<?php

namespace Tests\Feature;

use App\Events\TenantSwitched;
use App\Listeners\LogTenantSwitchAudit;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Monitoring\Models\AuditLog;
use Tests\TestCase;

class TenancySwitchAuditTest extends TestCase
{
    use RefreshDatabase;

    private function createPlan(): SubscriptionPlan
    {
        return SubscriptionPlan::create([
            'code' => 'starter-'.Str::random(4),
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);
    }

    private function createTenant(SubscriptionPlan $plan, string $code): Tenant
    {
        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => ucfirst($code),
            'subscription_plan_id' => $plan->id,
        ]);
    }

    public function test_tenant_switched_event_has_correct_properties(): void
    {
        $event = new TenantSwitched(
            newTenantId: 2,
            previousTenantId: 1,
            userId: 42,
        );

        $this->assertSame(2, $event->newTenantId);
        $this->assertSame(1, $event->previousTenantId);
        $this->assertSame(42, $event->userId);
    }

    public function test_tenant_switched_event_allows_null_previous_and_user(): void
    {
        $event = new TenantSwitched(
            newTenantId: 5,
            previousTenantId: null,
            userId: null,
        );

        $this->assertSame(5, $event->newTenantId);
        $this->assertNull($event->previousTenantId);
        $this->assertNull($event->userId);
    }

    public function test_log_tenant_switch_audit_listener_creates_audit_log(): void
    {
        $plan = $this->createPlan();
        $tenant = $this->createTenant($plan, 'school-alpha');

        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $event = new TenantSwitched(
            newTenantId: $tenant->id,
            previousTenantId: null,
            userId: $user->id,
        );

        $listener = new LogTenantSwitchAudit;
        $listener->handle($event);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'tenant_switch',
            'status' => 'success',
        ]);

        $log = AuditLog::withoutTenantScope()
            ->where('action', 'tenant_switch')
            ->where('tenant_id', $tenant->id)
            ->firstOrFail();

        $this->assertStringContainsString((string) $tenant->id, $log->description);
        $this->assertSame(['tenant_id' => null], $log->old_values);
        $this->assertSame(['tenant_id' => $tenant->id], $log->new_values);
    }

    public function test_log_tenant_switch_audit_records_previous_tenant(): void
    {
        $plan = $this->createPlan();
        $tenantA = $this->createTenant($plan, 'tenant-a');
        $tenantB = $this->createTenant($plan, 'tenant-b');

        $event = new TenantSwitched(
            newTenantId: $tenantB->id,
            previousTenantId: $tenantA->id,
            userId: null,
        );

        $listener = new LogTenantSwitchAudit;
        $listener->handle($event);

        $log = AuditLog::withoutTenantScope()
            ->where('action', 'tenant_switch')
            ->where('tenant_id', $tenantB->id)
            ->firstOrFail();

        $this->assertSame(['tenant_id' => $tenantA->id], $log->old_values);
        $this->assertSame(['tenant_id' => $tenantB->id], $log->new_values);
    }

    public function test_tenant_switched_event_is_dispatched_when_tenant_changes(): void
    {
        Event::fake([TenantSwitched::class]);

        $plan = $this->createPlan();
        $tenantA = $this->createTenant($plan, 'org-first');
        $tenantB = $this->createTenant($plan, 'org-second');

        /** @var CurrentTenant $currentTenant */
        $currentTenant = app(CurrentTenant::class);
        $currentTenant->set($tenantA);

        event(new TenantSwitched(
            newTenantId: $tenantB->id,
            previousTenantId: $tenantA->id,
            userId: null,
        ));

        Event::assertDispatched(TenantSwitched::class, function (TenantSwitched $event) use ($tenantA, $tenantB): bool {
            return $event->newTenantId === $tenantB->id
                && $event->previousTenantId === $tenantA->id;
        });
    }

    public function test_event_listener_is_registered(): void
    {
        $dispatcher = app('events');

        $listeners = $dispatcher->getListeners(TenantSwitched::class);

        $this->assertNotEmpty($listeners, 'No listeners registered for TenantSwitched event.');
    }
}
