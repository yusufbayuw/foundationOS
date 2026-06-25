<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Services\ApplicantPromotionService;
use Modules\School\Models\Student;
use Tests\TestCase;

class EnrollmentSecurityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_api_rejects_admission_period_from_another_tenant(): void
    {
        [$tenantA, $tokenA, $periodA] = $this->makeTenantWithToken('tenant-a');
        [$tenantB, , $periodB] = $this->makeTenantWithToken('tenant-b');

        $response = $this->withToken($tokenA)->postJson('/api/v1/applicants', [
            'admission_period_id' => $periodB->id,
            'registration_number' => 'REG-CROSS',
            'full_name' => 'Cross Tenant Applicant',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'validation_failed');

        $this->assertDatabaseMissing('applicants', [
            'registration_number' => 'REG-CROSS',
            'tenant_id' => $tenantA->id,
        ]);
        $this->assertDatabaseMissing('applicants', [
            'registration_number' => 'REG-CROSS',
            'tenant_id' => $tenantB->id,
        ]);
    }

    public function test_api_accepts_admission_period_from_same_tenant(): void
    {
        [$tenant, $token, $period] = $this->makeTenantWithToken('tenant-ok');

        $response = $this->withToken($token)->postJson('/api/v1/applicants', [
            'admission_period_id' => $period->id,
            'registration_number' => 'REG-OK',
            'full_name' => 'Valid Applicant',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.registration_number', 'REG-OK');

        $this->assertDatabaseHas('applicants', [
            'tenant_id' => $tenant->id,
            'admission_period_id' => $period->id,
            'registration_number' => 'REG-OK',
        ]);
    }

    public function test_promotion_is_idempotent_and_does_not_duplicate_students(): void
    {
        [$tenant, , $applicant] = $this->makeAcceptedApplicant('idempotent');

        $service = app(ApplicantPromotionService::class);

        $first = $service->promote($applicant);
        $second = $service->promote($applicant->fresh());

        $this->assertNotNull($first);
        $this->assertSame($first->id, $second?->id);
        $this->assertSame(1, Student::withoutTenantScope()->where('tenant_id', $tenant->id)->count());
    }

    public function test_applicant_query_scoped_to_current_tenant(): void
    {
        [$tenantA, , $applicantA] = $this->makeAcceptedApplicant('scope-a');
        [$tenantB, , $applicantB] = $this->makeAcceptedApplicant('scope-b');

        app(CurrentTenant::class)->set($tenantA);

        $this->assertTrue(Applicant::query()->whereKey($applicantA->id)->exists());
        $this->assertFalse(Applicant::query()->whereKey($applicantB->id)->exists());

        app(CurrentTenant::class)->forget();
    }

    /**
     * @return array{0: Tenant, 1: string, 2: AdmissionPeriod}
     */
    protected function makeTenantWithToken(string $code): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'enrollment-security-plan'],
            ['name' => 'Enrollment Security Plan', 'included_modules' => ['core', 'enrollment']],
        );

        $user = User::factory()->create();

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => "Tenant {$code}",
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'name' => 'School',
            'code' => strtoupper($code),
        ]);

        $period = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'PPDB',
            'code' => 'PPDB-'.$code,
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $created = $user->createToken('enrollment-security');
        $pat = PersonalAccessToken::find($created->accessToken->id);
        $pat->update(['tenant_id' => $tenant->id]);

        return [$tenant, $created->plainTextToken, $period];
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: Applicant}
     */
    protected function makeAcceptedApplicant(string $suffix): array
    {
        [$tenant, , $period] = $this->makeTenantWithToken("adm-{$suffix}");

        $organization = Organization::query()
            ->where('tenant_id', $tenant->id)
            ->firstOrFail();

        $applicant = Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $period->id,
            'registration_number' => 'REG-'.$suffix,
            'full_name' => 'Applicant '.$suffix,
            'status' => 'accepted',
        ]);

        return [$tenant, $organization, $applicant];
    }
}
