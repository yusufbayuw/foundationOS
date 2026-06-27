<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Department;
use Modules\Core\Models\Tenant;
use Modules\Core\Services\TenantDomain\TenantDomainAggregateService;
use Modules\Library\Models\Book;
use Modules\School\Models\Student;
use Tests\TestCase;

class TenantDomainDecouplingTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_aggregate_service_registers_all_domain_relations(): void
    {
        $aggregate = app(TenantDomainAggregateService::class);

        $expected = [
            'academicYears',
            'departments',
            'academicPeriods',
            'students',
            'books',
            'payments',
            'employees',
            'vendors',
            'courses',
            'assessments',
            'studentInvoices',
            'auditableLogs',
            'attachedFiles',
        ];

        foreach ($expected as $relationName) {
            $this->assertTrue(
                $aggregate->hasRelation($relationName),
                "Missing delegated relation [{$relationName}]",
            );
        }
    }

    public function test_tenant_domain_relations_resolve_through_eloquent_without_model_methods(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertFalse(method_exists(Tenant::class, 'students'));
        $this->assertTrue($tenant->isRelation('students'));
        $this->assertInstanceOf(HasMany::class, $tenant->students());
    }

    public function test_delegated_has_many_relations_return_scoped_query(): void
    {
        $tenant = Tenant::factory()->create();
        $otherTenant = Tenant::factory()->create();

        $student = Student::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'nis' => 'TENANT-A',
            'status' => 'active',
        ]);

        Student::withoutTenantScope()->create([
            'tenant_id' => $otherTenant->id,
            'nis' => 'TENANT-B',
            'status' => 'active',
        ]);

        $relationIds = $tenant->students()->pluck('id')->all();

        $this->assertSame([$student->id], $relationIds);
    }

    public function test_aggregate_service_query_object_returns_builder_scoped_by_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $otherTenant = Tenant::factory()->create();

        $book = Book::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'title' => 'Tenant Book',
            'authors' => ['Author'],
        ]);

        Book::withoutTenantScope()->create([
            'tenant_id' => $otherTenant->id,
            'title' => 'Other Book',
            'authors' => ['Author'],
        ]);

        $ids = app(TenantDomainAggregateService::class)
            ->query($tenant, 'books')
            ->pluck('id')
            ->all();

        $this->assertSame([$book->id], $ids);
    }

    public function test_delegated_relations_cover_finance_procurement_and_campus_examples(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertInstanceOf(HasMany::class, $tenant->payments());
        $this->assertInstanceOf(HasMany::class, $tenant->vendors());
        $this->assertInstanceOf(HasMany::class, $tenant->courses());
        $this->assertInstanceOf(HasMany::class, $tenant->assessments());
        $this->assertInstanceOf(HasMany::class, $tenant->employees());
        $this->assertInstanceOf(HasMany::class, $tenant->studentInvoices());
    }

    public function test_core_structure_relations_are_delegated_not_declared_on_tenant_model(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertFalse(method_exists(Tenant::class, 'academicYears'));
        $this->assertFalse(method_exists(Tenant::class, 'departments'));
        $this->assertTrue($tenant->isRelation('academicYears'));
        $this->assertTrue($tenant->isRelation('departments'));
        $this->assertInstanceOf(HasMany::class, $tenant->academicYears());
        $this->assertInstanceOf(HasMany::class, $tenant->departments());
    }

    public function test_morph_relations_remain_available_for_filament_relation_managers(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertInstanceOf(MorphMany::class, $tenant->auditableLogs());
        $this->assertInstanceOf(MorphMany::class, $tenant->attachedFiles());
    }

    public function test_tenant_can_eager_load_delegated_relations(): void
    {
        $tenant = Tenant::factory()->create();

        Student::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'nis' => 'EAGER-001',
            'status' => 'active',
        ]);

        $loadedTenant = Tenant::query()->with('students')->findOrFail($tenant->id);

        $this->assertTrue($loadedTenant->relationLoaded('students'));
        $this->assertCount(1, $loadedTenant->students);
    }

    public function test_core_structure_records_are_queryable_via_aggregate_service(): void
    {
        $tenant = Tenant::factory()->create();

        AcademicYear::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => '2025/2026',
            'code' => '2025-2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
        ]);

        Department::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'code' => 'IT',
            'name' => 'Information Technology',
        ]);

        $aggregate = app(TenantDomainAggregateService::class);

        $this->assertCount(1, $aggregate->query($tenant, 'academicYears')->get());
        $this->assertCount(1, $aggregate->query($tenant, 'departments')->get());
    }
}
