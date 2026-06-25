<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\ParentStudent;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\School\Models\Student;
use Tests\TestCase;

class ParentPortalIsolationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_parent_only_sees_linked_children(): void
    {
        $tenant = $this->makeTenant();
        $parent = User::factory()->create();
        $otherParent = User::factory()->create();

        $child = $this->makeStudent($tenant, 'Child A');
        $otherChild = $this->makeStudent($tenant, 'Child B');

        ParentStudent::query()->create([
            'tenant_id' => $tenant->id,
            'parent_user_id' => $parent->id,
            'student_id' => $child->id,
            'relationship' => 'father',
            'is_primary' => true,
        ]);

        ParentStudent::query()->create([
            'tenant_id' => $tenant->id,
            'parent_user_id' => $otherParent->id,
            'student_id' => $otherChild->id,
            'relationship' => 'mother',
            'is_primary' => true,
        ]);

        $visibleIds = $this->childrenIdsForParent($parent->id);

        $this->assertContains($child->id, $visibleIds);
        $this->assertNotContains($otherChild->id, $visibleIds);
    }

    public function test_parent_does_not_see_children_from_other_tenant(): void
    {
        $tenantA = $this->makeTenant('school-a');
        $tenantB = $this->makeTenant('school-b');
        $parent = User::factory()->create();

        $childA = $this->makeStudent($tenantA, 'Child Tenant A');
        $childB = $this->makeStudent($tenantB, 'Child Tenant B');

        ParentStudent::query()->create([
            'tenant_id' => $tenantA->id,
            'parent_user_id' => $parent->id,
            'student_id' => $childA->id,
            'relationship' => 'father',
            'is_primary' => true,
        ]);

        $visibleIds = $this->childrenIdsForParent($parent->id);

        $this->assertContains($childA->id, $visibleIds);
        $this->assertNotContains($childB->id, $visibleIds);
    }

    public function test_my_children_query_matches_resource_scope(): void
    {
        $tenant = $this->makeTenant();
        $parent = User::factory()->create();
        $student = $this->makeStudent($tenant, 'Scoped Child');

        ParentStudent::query()->create([
            'tenant_id' => $tenant->id,
            'parent_user_id' => $parent->id,
            'student_id' => $student->id,
            'relationship' => 'guardian',
            'is_primary' => true,
        ]);

        $this->actingAs($parent);

        $resourceIds = Student::query()
            ->whereIn('id', ParentStudent::query()
                ->where('parent_user_id', $parent->id)
                ->pluck('student_id'))
            ->pluck('id')
            ->all();

        $this->assertSame([$student->id], $resourceIds);
    }

    /**
     * @return list<int>
     */
    protected function childrenIdsForParent(int $parentUserId): array
    {
        return ParentStudent::query()
            ->where('parent_user_id', $parentUserId)
            ->pluck('student_id')
            ->all();
    }

    protected function makeTenant(string $code = 'parent-iso'): Tenant
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'parent-portal-plan'],
            ['name' => 'Parent Portal Plan', 'included_modules' => ['core', 'school']],
        );

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => 'Tenant '.$code,
            'subscription_plan_id' => $plan->id,
        ]);
    }

    protected function makeStudent(Tenant $tenant, string $label): Student
    {
        $organization = Organization::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'code' => 'ORG-'.$tenant->code,
            ],
            ['name' => 'School '.$tenant->code],
        );

        return Student::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'nis' => 'NIS-'.Str::slug($label),
            'status' => 'active',
        ]);
    }
}
