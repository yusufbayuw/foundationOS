<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Employee\Models\Employee;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Tests\TestCase;

class ApiResourceReadTest extends TestCase
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
            'code' => 'read-api-plan',
            'name' => 'Read API Plan',
            'included_modules' => ['core', 'school', 'campus', 'employee'],
        ]);

        $this->user = User::create([
            'name' => 'API Read User',
            'email' => 'api-read@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-read',
            'name' => 'Read Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        // Set tenant context so BelongsToTenant scope is applied correctly.
        app(CurrentTenant::class)->set($this->tenant);

        $this->organization = Organization::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test School',
            'code' => 'TS-001',
            'is_active' => true,
            'is_main' => true,
        ]);

        // Create a tenant-scoped PAT so ResolveApiTenant middleware sets the tenant.
        $createdToken = $this->user->createToken('read-token');
        $pat = PersonalAccessToken::find($createdToken->accessToken->id);
        $pat->update(['tenant_id' => $this->tenant->id]);
        $this->token = $createdToken->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    // ─── Organizations ───────────────────────────────────────────────────────

    public function test_organizations_list_returns_paginated_data(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/organizations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'code', 'name', 'is_active', 'created_at']],
                'meta' => ['per_page', 'has_more'],
            ]);
    }

    public function test_organizations_show_returns_single_record(): void
    {
        $response = $this->withToken($this->token)->getJson("/api/v1/organizations/{$this->organization->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->organization->id)
            ->assertJsonPath('data.code', 'TS-001');
    }

    public function test_organizations_filter_by_is_active(): void
    {
        Organization::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Inactive School',
            'code' => 'TS-002',
            'is_active' => false,
            'is_main' => false,
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/organizations?filter[is_active]=true');

        $response->assertStatus(200);
        $ids = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertContains($this->organization->id, $ids);
    }

    public function test_organizations_filter_by_name_search(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/organizations?filter[name]=Test School');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->toArray();
        $this->assertContains('Test School', $names);
    }

    public function test_organizations_show_returns_404_for_unknown_id(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/organizations/999999');

        $response->assertStatus(404);
    }

    // ─── Students ────────────────────────────────────────────────────────────

    public function test_students_list_returns_paginated_data(): void
    {
        Student::create([
            'tenant_id' => $this->tenant->id,
            'nis' => 'NIS-001',
            'status' => 'active',
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/students');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'nis', 'status', 'created_at']],
                'meta' => ['per_page', 'has_more'],
            ]);
    }

    public function test_students_filter_by_status(): void
    {
        Student::create([
            'tenant_id' => $this->tenant->id,
            'nis' => 'NIS-ACT',
            'status' => 'active',
        ]);

        Student::create([
            'tenant_id' => $this->tenant->id,
            'nis' => 'NIS-GRAD',
            'status' => 'graduated',
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/students?filter[status]=active');

        $response->assertStatus(200);
        $statuses = collect($response->json('data'))->pluck('status')->unique()->toArray();
        $this->assertSame(['active'], $statuses);
    }

    public function test_students_show_with_include(): void
    {
        $student = Student::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'nis' => 'NIS-INC',
            'status' => 'active',
        ]);

        $response = $this->withToken($this->token)->getJson("/api/v1/students/{$student->id}?include=organization");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $student->id)
            ->assertJsonStructure(['data' => ['organization' => ['id', 'name']]]);
    }

    // ─── College Students ────────────────────────────────────────────────────

    public function test_college_students_list_returns_paginated_data(): void
    {
        CollageStudent::create([
            'tenant_id' => $this->tenant->id,
            'student_number' => 'NPM-001',
            'full_name' => 'Budi Santoso',
            'status' => 'active',
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/college-students');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'student_number', 'full_name', 'status', 'created_at']],
                'meta' => ['per_page', 'has_more'],
            ]);
    }

    public function test_college_students_filter_by_full_name_search(): void
    {
        CollageStudent::create([
            'tenant_id' => $this->tenant->id,
            'student_number' => 'NPM-002',
            'full_name' => 'Siti Rahayu',
            'status' => 'active',
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/college-students?filter[full_name]=Siti');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('full_name')->toArray();
        $this->assertContains('Siti Rahayu', $names);
    }

    public function test_college_students_show(): void
    {
        $student = CollageStudent::create([
            'tenant_id' => $this->tenant->id,
            'student_number' => 'NPM-003',
            'full_name' => 'Agus Setiawan',
            'status' => 'active',
        ]);

        $response = $this->withToken($this->token)->getJson("/api/v1/college-students/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.student_number', 'NPM-003')
            ->assertJsonPath('data.full_name', 'Agus Setiawan');
    }

    // ─── School Classes ───────────────────────────────────────────────────────

    public function test_classes_list_returns_paginated_data(): void
    {
        $year = AcademicYear::create([
            'tenant_id' => $this->tenant->id,
            'name' => '2026/2027',
            'code' => 'AY-2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $this->tenant->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester Ganjil',
            'code' => 'SG-2026',
            'type' => 'semester',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        SchoolClass::create([
            'tenant_id' => $this->tenant->id,
            'academic_period_id' => $period->id,
            'name' => 'Kelas 10A',
            'code' => 'K10A',
            'is_active' => true,
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/classes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'code', 'is_active', 'created_at']],
                'meta' => ['per_page', 'has_more'],
            ]);
    }

    public function test_classes_filter_by_is_active(): void
    {
        $year = AcademicYear::create([
            'tenant_id' => $this->tenant->id,
            'name' => '2026/2027',
            'code' => 'AY-2026B',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $this->tenant->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester Genap',
            'code' => 'SGEN-2026',
            'type' => 'semester',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        SchoolClass::create([
            'tenant_id' => $this->tenant->id,
            'academic_period_id' => $period->id,
            'name' => 'Kelas 11B',
            'code' => 'K11B',
            'is_active' => true,
        ]);

        SchoolClass::create([
            'tenant_id' => $this->tenant->id,
            'academic_period_id' => $period->id,
            'name' => 'Kelas 11C',
            'code' => 'K11C',
            'is_active' => false,
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/classes?filter[is_active]=true');

        $response->assertStatus(200);
        $activeValues = collect($response->json('data'))->pluck('is_active')->unique()->toArray();
        $this->assertSame([true], $activeValues);
    }

    // ─── Courses ─────────────────────────────────────────────────────────────

    public function test_courses_list_returns_paginated_data(): void
    {
        Course::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'CS101',
            'name' => 'Algoritma & Pemrograman',
            'credits' => 3,
            'is_active' => true,
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'code', 'name', 'credits', 'is_active', 'created_at']],
                'meta' => ['per_page', 'has_more'],
            ]);
    }

    public function test_courses_filter_by_is_active(): void
    {
        Course::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'CS201',
            'name' => 'Struktur Data',
            'credits' => 3,
            'is_active' => true,
        ]);

        Course::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'CS202',
            'name' => 'Pemrograman Web Lama',
            'credits' => 2,
            'is_active' => false,
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/courses?filter[is_active]=true');

        $response->assertStatus(200);
        $activeValues = collect($response->json('data'))->pluck('is_active')->unique()->toArray();
        $this->assertSame([true], $activeValues);
    }

    public function test_courses_show(): void
    {
        $course = Course::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'CS301',
            'name' => 'Basis Data',
            'credits' => 3,
            'is_active' => true,
        ]);

        $response = $this->withToken($this->token)->getJson("/api/v1/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.code', 'CS301')
            ->assertJsonPath('data.name', 'Basis Data');
    }

    // ─── Employees ───────────────────────────────────────────────────────────

    public function test_employees_list_returns_paginated_data(): void
    {
        Employee::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'employee_number' => 'EMP-001',
            'full_name' => 'Hendra Wijaya',
            'join_date' => '2024-01-01',
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/employees');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'employee_number', 'full_name', 'created_at']],
                'meta' => ['per_page', 'has_more'],
            ]);
    }

    public function test_employees_filter_by_full_name_search(): void
    {
        Employee::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'employee_number' => 'EMP-002',
            'full_name' => 'Dewi Kusuma',
            'join_date' => '2024-02-01',
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/employees?filter[full_name]=Dewi');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('full_name')->toArray();
        $this->assertContains('Dewi Kusuma', $names);
    }

    public function test_employees_show_with_include_organization(): void
    {
        $employee = Employee::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'employee_number' => 'EMP-003',
            'full_name' => 'Rudi Hartono',
            'join_date' => '2024-03-01',
        ]);

        $response = $this->withToken($this->token)->getJson("/api/v1/employees/{$employee->id}?include=organization");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $employee->id)
            ->assertJsonStructure(['data' => ['organization' => ['id', 'name']]]);
    }

    // ─── Auth & Structure ────────────────────────────────────────────────────

    public function test_unauthenticated_request_to_resource_endpoint_returns_401(): void
    {
        $response = $this->getJson('/api/v1/organizations');

        $response->assertStatus(401)
            ->assertJson(['error' => ['code' => 'unauthenticated']]);
    }

    public function test_cursor_pagination_meta_is_present(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/organizations?per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure(['meta' => ['per_page', 'has_more', 'next_cursor', 'prev_cursor']])
            ->assertJsonPath('meta.per_page', 5);
    }
}
