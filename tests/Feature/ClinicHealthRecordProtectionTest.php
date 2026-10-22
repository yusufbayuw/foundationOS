<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Clinic\Models\HealthRecord;
use Modules\Clinic\Policies\HealthRecordPolicy;
use Modules\Core\Models\Tenant;
use Tests\TestCase;

class ClinicHealthRecordProtectionTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_medical_record_policy_rejects_foreign_tenant_even_with_permission(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = $this->userWithPermission();
        $policy = new HealthRecordPolicy;

        app(CurrentTenant::class)->set($tenantA);

        $ownRecord = new HealthRecord(['tenant_id' => $tenantA->id]);
        $foreignRecord = new HealthRecord(['tenant_id' => $tenantB->id]);

        $this->assertTrue($policy->view($user, $ownRecord));
        $this->assertFalse($policy->view($user, $foreignRecord));
        $this->assertFalse($policy->update($user, $foreignRecord));
        $this->assertFalse($policy->delete($user, $foreignRecord));
        $this->assertFalse($policy->restore($user, $foreignRecord));
    }

    public function test_medical_history_cannot_be_bulk_purged_or_permanently_deleted(): void
    {
        $tenant = Tenant::factory()->create();
        app(CurrentTenant::class)->set($tenant);

        $user = $this->userWithPermission();
        $record = new HealthRecord(['tenant_id' => $tenant->id]);
        $policy = new HealthRecordPolicy;

        $this->assertFalse($policy->deleteAny($user));
        $this->assertFalse($policy->forceDelete($user, $record));
        $this->assertFalse($policy->forceDeleteAny($user));
        $this->assertFalse($policy->restoreAny($user));
        $this->assertFalse($policy->replicate($user, $record));
    }

    private function userWithPermission(): AuthUser
    {
        return new class extends AuthUser
        {
            public function can($abilities, $arguments = [])
            {
                return true;
            }
        };
    }
}
