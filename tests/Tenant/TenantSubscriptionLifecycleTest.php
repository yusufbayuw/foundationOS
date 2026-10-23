<?php

namespace Tests\Tenant;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\Tenant;
use Tests\TestCase;

class TenantSubscriptionLifecycleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_expired_trial_locks_tenant_for_panel_and_api(): void
    {
        $tenant = Tenant::factory()->create([
            'status' => 'trial',
            'trial_ends_at' => now()->subMinute(),
        ]);

        $this->assertFalse($tenant->isSubscriptionActive());
        $this->assertTrue($tenant->isLocked());
    }

    public function test_unexpired_trial_and_paid_subscription_allow_access(): void
    {
        $trial = Tenant::factory()->create([
            'status' => 'trial',
            'trial_ends_at' => now()->addDay(),
        ]);
        $paid = Tenant::factory()->create([
            'status' => 'active',
            'subscription_expires_at' => now()->addMonth(),
        ]);

        $this->assertTrue($trial->isSubscriptionActive());
        $this->assertFalse($trial->isLocked());
        $this->assertTrue($paid->isSubscriptionActive());
        $this->assertFalse($paid->isLocked());
    }

    public function test_expired_paid_subscription_and_unknown_status_are_locked(): void
    {
        $tenant = Tenant::factory()->create([
            'status' => 'active',
            'subscription_expires_at' => now()->subDay(),
        ]);
        $unknown = Tenant::factory()->create(['status' => 'unknown']);

        $this->assertFalse($tenant->isSubscriptionActive());
        $this->assertTrue($tenant->isLocked());
        $this->assertTrue($unknown->isLocked());
    }

    public function test_past_due_grace_period_is_preserved(): void
    {
        $tenant = Tenant::factory()->create([
            'status' => 'past_due',
            'grace_period_ends_at' => now()->addDay(),
        ]);

        $this->assertFalse($tenant->isLocked());
        $tenant->update(['grace_period_ends_at' => now()->subMinute()]);
        $this->assertTrue($tenant->fresh()->isLocked());
    }

    public function test_legacy_active_tenant_without_expiry_remains_accessible(): void
    {
        $tenant = Tenant::factory()->create([
            'status' => 'active',
            'subscription_expires_at' => null,
        ]);

        $this->assertTrue($tenant->isSubscriptionActive());
        $this->assertFalse($tenant->isLocked());
    }
}
