<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Employee\Models\Employee;
use Modules\Enrollment\Models\AdmissionPeriod;
use Tests\TestCase;

class WriteApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Organization $organization;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'write-api-plan',
            'name' => 'Write API Plan',
            'included_modules' => ['core', 'enrollment', 'employee'],
        ]);

        $this->user = User::create([
            'name' => 'Write API User',
            'email' => 'write-api@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-write',
            'name' => 'Write Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $this->organization = Organization::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Write School',
            'code' => 'WS-001',
            'is_active' => true,
            'is_main' => true,
        ]);

        $created = $this->user->createToken('write-token');
        $pat = PersonalAccessToken::find($created->accessToken->id);
        $pat->update(['tenant_id' => $this->tenant->id]);
        $this->token = $created->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    // ─── Applicant Write ─────────────────────────────────────────────────────

    public function test_post_applicants_creates_record_and_returns_201(): void
    {
        $period = AdmissionPeriod::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'name' => 'Admisi 2026',
            'code' => 'ADM-2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $response = $this->withToken($this->token)->postJson('/api/v1/applicants', [
            'admission_period_id' => $period->id,
            'registration_number' => 'REG-001',
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'gender' => 'male',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.registration_number', 'REG-001')
            ->assertJsonPath('data.full_name', 'Budi Santoso')
            ->assertJsonPath('data.status', 'registered');

        $this->assertDatabaseHas('applicants', [
            'tenant_id' => $this->tenant->id,
            'registration_number' => 'REG-001',
        ]);
    }

    public function test_post_applicants_validates_required_fields(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/applicants', []);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'validation_failed');
    }

    // ─── Leave Request Write ──────────────────────────────────────────────────

    public function test_post_leave_requests_creates_record_and_returns_201(): void
    {
        $employee = Employee::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'employee_number' => 'EMP-001',
            'full_name' => 'Hendra Wijaya',
            'join_date' => '2024-01-01',
        ]);

        $response = $this->withToken($this->token)->postJson('/api/v1/leave-requests', [
            'employee_id' => $employee->id,
            'leave_type' => 'annual',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-05',
            'total_days' => 5,
            'reason' => 'Liburan tahunan',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.employee_id', $employee->id)
            ->assertJsonPath('data.leave_type', 'annual')
            ->assertJsonPath('data.total_days', 5)
            ->assertJsonPath('data.status', 'draft');

        $this->assertDatabaseHas('leave_requests', [
            'tenant_id' => $this->tenant->id,
            'employee_id' => $employee->id,
            'leave_type' => 'annual',
        ]);
    }

    public function test_post_leave_requests_validates_end_date_after_start(): void
    {
        $employee = Employee::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'employee_number' => 'EMP-002',
            'full_name' => 'Dewi Kusuma',
            'join_date' => '2024-01-01',
        ]);

        $response = $this->withToken($this->token)->postJson('/api/v1/leave-requests', [
            'employee_id' => $employee->id,
            'leave_type' => 'sick',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-05', // before start_date
            'total_days' => 1,
            'reason' => 'Sakit',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'validation_failed');
    }

    // ─── Idempotency Key ─────────────────────────────────────────────────────

    public function test_same_idempotency_key_same_body_returns_cached_response(): void
    {
        $period = AdmissionPeriod::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'name' => 'Admisi 2026 Idem',
            'code' => 'ADM-IDEM',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $body = [
            'admission_period_id' => $period->id,
            'registration_number' => 'REG-IDEM',
            'full_name' => 'Test Idempotency',
        ];

        $headers = ['Idempotency-Key' => 'key-abc-123'];

        // First request
        $first = $this->withToken($this->token)
            ->withHeaders($headers)
            ->postJson('/api/v1/applicants', $body);

        $first->assertStatus(201);

        // Second request with same key and body → should be replayed
        $second = $this->withToken($this->token)
            ->withHeaders($headers)
            ->postJson('/api/v1/applicants', $body);

        $second->assertStatus(201);
        $this->assertSame($first->json('data.registration_number'), $second->json('data.registration_number'));

        // Only one record should be created (idempotent)
        $this->assertDatabaseCount('applicants', 1);
    }

    public function test_same_idempotency_key_different_body_returns_409(): void
    {
        $period = AdmissionPeriod::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'name' => 'Admisi 2026 Conflict',
            'code' => 'ADM-CONF',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $idempotencyKey = 'conflict-key-xyz';

        // First request
        $this->withToken($this->token)
            ->withHeaders(['Idempotency-Key' => $idempotencyKey])
            ->postJson('/api/v1/applicants', [
                'admission_period_id' => $period->id,
                'registration_number' => 'REG-CONF-1',
                'full_name' => 'First Body',
            ])
            ->assertStatus(201);

        // Second request with same key but different body → 409
        $this->withToken($this->token)
            ->withHeaders(['Idempotency-Key' => $idempotencyKey])
            ->postJson('/api/v1/applicants', [
                'admission_period_id' => $period->id,
                'registration_number' => 'REG-CONF-2',
                'full_name' => 'Different Body',
            ])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'idempotency_conflict');
    }

    public function test_unauthenticated_write_request_returns_401(): void
    {
        $response = $this->postJson('/api/v1/applicants', [
            'admission_period_id' => 1,
            'registration_number' => 'REG-UNAUTH',
            'full_name' => 'Nobody',
        ]);

        $response->assertStatus(401);
    }
}
