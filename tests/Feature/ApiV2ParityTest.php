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
use Tests\TestCase;

class ApiV2ParityTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Organization $organization;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'api-v2-parity',
            'name' => 'API V2 Parity',
            'included_modules' => ['core', 'enrollment', 'school'],
        ]);

        $this->user = User::factory()->create();

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-v2-parity',
            'name' => 'V2 Parity Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $this->organization = Organization::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'V2 School',
            'code' => 'V2S',
            'is_active' => true,
            'is_main' => true,
        ]);

        $created = $this->user->createToken('v2-parity');
        PersonalAccessToken::query()
            ->whereKey($created->accessToken->id)
            ->update(['tenant_id' => $this->tenant->id]);

        $this->token = $created->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_v2_read_endpoints_include_api_version_meta(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v2/organizations');

        $response->assertOk()
            ->assertJsonPath('meta.api_version', 'v2')
            ->assertJsonStructure([
                'data' => [['id', 'code', 'name']],
                'meta' => ['api_version', 'per_page', 'has_more'],
            ]);
    }

    public function test_v2_show_endpoint_includes_api_version_meta(): void
    {
        $response = $this->withToken($this->token)->getJson("/api/v2/organizations/{$this->organization->id}");

        $response->assertOk()
            ->assertJsonPath('meta.api_version', 'v2')
            ->assertJsonPath('data.code', 'V2S');
    }

    public function test_v2_write_endpoint_includes_api_version_meta(): void
    {
        $period = AdmissionPeriod::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'name' => 'PPDB V2',
            'code' => 'PPDB-V2',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $response = $this->withToken($this->token)->postJson('/api/v2/applicants', [
            'admission_period_id' => $period->id,
            'registration_number' => 'REG-V2-001',
            'full_name' => 'Calon Siswa V2',
        ]);

        $response->assertCreated()
            ->assertJsonPath('meta.api_version', 'v2')
            ->assertJsonPath('data.registration_number', 'REG-V2-001');
    }

    public function test_v2_me_matches_v1_payload_with_version_meta(): void
    {
        $v1 = $this->withToken($this->token)->getJson('/api/v1/me')->json('data');
        $v2 = $this->withToken($this->token)->getJson('/api/v2/me');

        $v2->assertOk()
            ->assertJsonPath('meta.api_version', 'v2')
            ->assertJsonPath('data.email', $v1['email']);
    }
}
