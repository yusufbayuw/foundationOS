<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\School\Models\Attendance;
use Modules\School\Models\Student;
use Tests\TestCase;

class MobileApiTest extends TestCase
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
            'code' => 'mobile-plan',
            'name' => 'Mobile Plan',
            'included_modules' => ['core', 'school'],
        ]);

        $this->user = User::create([
            'name' => 'Mobile User',
            'email' => 'mobile@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-mobile',
            'name' => 'Mobile Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $this->organization = Organization::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Mobile School',
            'code' => 'MS-001',
            'is_active' => true,
            'is_main' => true,
        ]);

        $created = $this->user->createToken('mobile-token');
        $pat = PersonalAccessToken::find($created->accessToken->id);
        $pat->update(['tenant_id' => $this->tenant->id]);
        $this->token = $created->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    // ─── Device Registration ──────────────────────────────────────────────────

    public function test_post_devices_registers_push_token(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/devices', [
            'token' => 'fcm-token-abc123',
            'platform' => 'android',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.platform', 'android')
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('devices', [
            'user_id' => $this->user->id,
            'token' => 'fcm-token-abc123',
            'platform' => 'android',
        ]);
    }

    public function test_post_devices_is_idempotent_on_same_token(): void
    {
        // Register twice with same token
        $this->withToken($this->token)->postJson('/api/v1/devices', [
            'token' => 'fcm-same-token',
            'platform' => 'ios',
        ])->assertStatus(201);

        $this->withToken($this->token)->postJson('/api/v1/devices', [
            'token' => 'fcm-same-token',
            'platform' => 'ios',
        ])->assertStatus(201);

        $this->assertDatabaseCount('devices', 1);
    }

    public function test_delete_device_deactivates_token(): void
    {
        Device::create([
            'user_id' => $this->user->id,
            'token' => 'fcm-delete-me',
            'platform' => 'android',
            'is_active' => true,
        ]);

        $this->withToken($this->token)->deleteJson('/api/v1/devices/fcm-delete-me')
            ->assertStatus(204);

        $this->assertDatabaseHas('devices', [
            'token' => 'fcm-delete-me',
            'is_active' => false,
        ]);
    }

    public function test_post_devices_validates_platform(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/devices', [
            'token' => 'some-token',
            'platform' => 'windows', // not allowed
        ]);

        $response->assertStatus(422)->assertJsonPath('error.code', 'validation_failed');
    }

    // ─── Student Dashboard ────────────────────────────────────────────────────

    public function test_student_dashboard_returns_bundled_data(): void
    {
        $student = Student::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'nis' => 'NIS-DASH-001',
            'status' => 'active',
        ]);

        // Create some attendance records
        Attendance::create([
            'tenant_id' => $this->tenant->id,
            'student_id' => $student->id,
            'attendance_date' => now()->subDays(1)->toDateString(),
            'status' => 'present',
        ]);

        Attendance::create([
            'tenant_id' => $this->tenant->id,
            'student_id' => $student->id,
            'attendance_date' => now()->subDays(2)->toDateString(),
            'status' => 'absent',
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/v1/students/{$student->id}/dashboard");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'student' => ['id', 'nis', 'status'],
                    'attendance' => ['summary' => ['present', 'absent', 'late', 'sick'], 'recent'],
                    'grades' => ['recent'],
                    'fees' => ['outstanding', 'total_outstanding'],
                    'generated_at',
                ],
            ])
            ->assertJsonPath('data.student.id', $student->id)
            ->assertJsonPath('data.attendance.summary.present', 1)
            ->assertJsonPath('data.attendance.summary.absent', 1);
    }

    public function test_student_dashboard_returns_404_for_unknown_student(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/v1/students/999999/dashboard');

        $response->assertStatus(404);
    }

    public function test_student_dashboard_response_is_cached(): void
    {
        $student = Student::create([
            'tenant_id' => $this->tenant->id,
            'nis' => 'NIS-CACHE-001',
            'status' => 'active',
        ]);

        // First call
        $first = $this->withToken($this->token)
            ->getJson("/api/v1/students/{$student->id}/dashboard");
        $first->assertStatus(200);

        $firstGeneratedAt = $first->json('data.generated_at');

        // Second call (within cache TTL) — same generated_at
        $second = $this->withToken($this->token)
            ->getJson("/api/v1/students/{$student->id}/dashboard");
        $second->assertStatus(200);

        $this->assertSame($firstGeneratedAt, $second->json('data.generated_at'));
    }

    public function test_unauthenticated_device_registration_returns_401(): void
    {
        $this->postJson('/api/v1/devices', ['token' => 'x', 'platform' => 'android'])
            ->assertStatus(401);
    }

    // ─── Backward compatibility ───────────────────────────────────────────────

    public function test_v1_api_structure_is_consistent_across_endpoints(): void
    {
        // All v1 list endpoints must return data + meta structure
        $endpoints = [
            '/api/v1/organizations',
            '/api/v1/students',
            '/api/v1/employees',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->withToken($this->token)->getJson($endpoint);
            $response->assertStatus(200)
                ->assertJsonStructure(['data', 'meta' => ['per_page', 'has_more']]);
        }
    }
}
