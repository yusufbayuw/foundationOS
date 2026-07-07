<?php

namespace Tests\Tenant;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Campus\Models\Course;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Employee\Models\Employee;
use Modules\School\Models\Student;
use Tests\TestCase;

class ApiTenantIsolationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_api_token_with_tenant_id_only_lists_students_from_that_tenant(): void
    {
        $tenantA = Tenant::factory()->create(['code' => 'api-a']);
        $tenantB = Tenant::factory()->create(['code' => 'api-b']);
        $user = User::factory()->create();

        Student::withoutTenantScope()->create([
            'tenant_id' => $tenantA->getKey(),
            'nis' => 'API-A-001',
            'status' => 'active',
        ]);

        Student::withoutTenantScope()->create([
            'tenant_id' => $tenantB->getKey(),
            'nis' => 'API-B-001',
            'status' => 'active',
        ]);

        $response = $this->withToken($this->createTenantToken($user, $tenantA))
            ->getJson('/api/v1/students');

        $response->assertOk();

        $this->assertSame(['API-A-001'], collect($response->json('data'))->pluck('nis')->all());
    }

    public function test_student_show_returns_404_for_record_outside_token_tenant(): void
    {
        $tenantA = Tenant::factory()->create(['code' => 'show-a']);
        $tenantB = Tenant::factory()->create(['code' => 'show-b']);
        $user = User::factory()->create();

        $visibleStudent = Student::withoutTenantScope()->create([
            'tenant_id' => $tenantA->getKey(),
            'nis' => 'VISIBLE-001',
            'status' => 'active',
        ]);

        $foreignStudent = Student::withoutTenantScope()->create([
            'tenant_id' => $tenantB->getKey(),
            'nis' => 'HIDDEN-001',
            'status' => 'active',
        ]);

        $token = $this->createTenantToken($user, $tenantA);

        $this->withToken($token)
            ->getJson("/api/v1/students/{$visibleStudent->getKey()}")
            ->assertOk()
            ->assertJsonPath('data.nis', 'VISIBLE-001');

        $this->withToken($token)
            ->getJson("/api/v1/students/{$foreignStudent->getKey()}")
            ->assertNotFound();
    }

    public function test_show_endpoints_return_404_for_records_outside_token_tenant(): void
    {
        $tenantA = Tenant::factory()->create(['code' => 'resources-a']);
        $tenantB = Tenant::factory()->create(['code' => 'resources-b']);
        $user = User::factory()->create();
        $token = $this->createTenantToken($user, $tenantA);

        $foreignOrganization = Organization::withoutTenantScope()->create([
            'tenant_id' => $tenantB->getKey(),
            'name' => 'Foreign Organization',
            'code' => 'FOREIGN-ORG',
        ]);
        $foreignCourse = Course::withoutTenantScope()->create([
            'tenant_id' => $tenantB->getKey(),
            'code' => 'FOREIGN-COURSE',
            'name' => 'Foreign Course',
        ]);
        $foreignEmployee = Employee::withoutTenantScope()->create([
            'tenant_id' => $tenantB->getKey(),
            'employee_number' => 'FOREIGN-EMPLOYEE',
            'organization_id' => $foreignOrganization->getKey(),
            'user_id' => $user->getKey(),
            'full_name' => 'Foreign Employee',
            'join_date' => '2026-01-01',
        ]);

        $this->withToken($token)
            ->getJson("/api/v1/organizations/{$foreignOrganization->getKey()}")
            ->assertNotFound();

        $this->withToken($token)
            ->getJson("/api/v1/courses/{$foreignCourse->getKey()}")
            ->assertNotFound();

        $this->withToken($token)
            ->getJson("/api/v1/employees/{$foreignEmployee->getKey()}")
            ->assertNotFound();
    }

    public function test_route_model_binding_returns_404_for_invalid_student_record(): void
    {
        $tenant = Tenant::factory()->create(['code' => 'invalid-binding']);
        $user = User::factory()->create();

        $this->withToken($this->createTenantToken($user, $tenant))
            ->getJson('/api/v1/students/not-a-valid-student')
            ->assertNotFound();
    }

    private function createTenantToken(User $user, Tenant $tenant): string
    {
        $token = $user->createToken('tenant-isolation-token');

        PersonalAccessToken::query()
            ->whereKey($token->accessToken->getKey())
            ->update(['tenant_id' => $tenant->getKey()]);

        return $token->plainTextToken;
    }
}
