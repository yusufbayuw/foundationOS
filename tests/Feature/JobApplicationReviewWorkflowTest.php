<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use DomainException;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Modules\Alumni\Database\Factories\JobPostingFactory;
use Modules\Alumni\Filament\Resources\JobApplications\Pages\EditJobApplication;
use Modules\Alumni\Models\JobApplication;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Services\ApplicationModuleCatalog;
use Modules\Core\Services\TenantModuleProvisioner;
use Spatie\Activitylog\Models\Activity;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class JobApplicationReviewWorkflowTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        Filament::setTenant(null);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_reviewer_can_transition_an_application_with_an_audited_causer(): void
    {
        ['application' => $application, 'reviewer' => $reviewer] = $this->makeApplication();

        $application->transitionStatus(JobApplication::STATUS_IN_REVIEW, $reviewer);

        $this->assertSame(JobApplication::STATUS_IN_REVIEW, $application->refresh()->status);

        $activity = Activity::query()->forSubject($application)->latest('id')->first();

        $this->assertNotNull($activity);
        $this->assertSame('updated', $activity->event);
        $this->assertSame($reviewer->getKey(), $activity->causer_id);
        $this->assertSame(
            JobApplication::STATUS_IN_REVIEW,
            $activity->attribute_changes->get('attributes')['status'] ?? null,
        );
    }

    public function test_invalid_status_transition_is_rejected_without_mutating_the_application(): void
    {
        ['application' => $application, 'reviewer' => $reviewer] = $this->makeApplication();
        $activityCount = Activity::query()->forSubject($application)->count();

        try {
            $application->transitionStatus(JobApplication::STATUS_ACCEPTED, $reviewer);
            $this->fail('An invalid status transition should throw a domain exception.');
        } catch (DomainException) {
            $this->assertSame(JobApplication::STATUS_SUBMITTED, $application->refresh()->status);
        }

        $this->assertSame($activityCount, Activity::query()->forSubject($application)->count());
    }

    public function test_filament_edit_page_uses_the_validated_review_transition(): void
    {
        [
            'application' => $application,
            'reviewer' => $reviewer,
            'tenant' => $tenant,
            'organization' => $organization,
            'role' => $role,
        ] = $this->makeApplication();

        $this->bootstrapFilament($tenant, $organization, $role, $reviewer);

        Livewire::test(EditJobApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm(['status' => JobApplication::STATUS_IN_REVIEW])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertSame(JobApplication::STATUS_IN_REVIEW, $application->refresh()->status);
        $this->assertSame(
            $reviewer->getKey(),
            Activity::query()->forSubject($application)->latest('id')->value('causer_id'),
        );
    }

    public function test_filament_edit_page_rejects_a_tampered_status_transition(): void
    {
        [
            'application' => $application,
            'reviewer' => $reviewer,
            'tenant' => $tenant,
            'organization' => $organization,
            'role' => $role,
        ] = $this->makeApplication();

        $this->bootstrapFilament($tenant, $organization, $role, $reviewer);

        Livewire::test(EditJobApplication::class, ['record' => $application->getRouteKey()])
            ->fillForm(['status' => JobApplication::STATUS_ACCEPTED])
            ->call('save')
            ->assertHasFormErrors(['status']);

        $this->assertSame(JobApplication::STATUS_SUBMITTED, $application->refresh()->status);
    }

    /**
     * @return array{application: JobApplication, reviewer: User, tenant: Tenant, organization: Organization, role: TenantRole}
     */
    private function makeApplication(): array
    {
        $context = $this->makeTenantContext(['core', 'alumni']);
        $tenant = $context['tenant'];
        $reviewer = User::factory()->superAdmin()->create();
        $applicant = User::factory()->create();

        app(CurrentTenant::class)->set($tenant);

        $jobPosting = JobPostingFactory::new()->create([
            'tenant_id' => $tenant->getKey(),
        ]);

        $application = JobApplication::query()->create([
            'tenant_id' => $tenant->getKey(),
            'job_posting_id' => $jobPosting->getKey(),
            'user_id' => $applicant->getKey(),
            'cover_letter' => 'I am interested in this role.',
        ]);

        return [
            'application' => $application,
            'reviewer' => $reviewer,
            'tenant' => $tenant,
            'organization' => $context['organization'],
            'role' => $context['role'],
        ];
    }

    private function bootstrapFilament(
        Tenant $tenant,
        Organization $organization,
        TenantRole $role,
        User $reviewer,
    ): void {
        UserTenantRole::create([
            'user_id' => $reviewer->getKey(),
            'tenant_id' => $tenant->getKey(),
            'organization_id' => $organization->getKey(),
            'tenant_role_id' => $role->getKey(),
            'assigned_by' => $reviewer->getKey(),
            'is_primary' => true,
        ]);

        app(ApplicationModuleCatalog::class)->sync();
        app(TenantModuleProvisioner::class)->enableForTenant($tenant, ['core', 'alumni']);

        TenantModule::query()
            ->where('tenant_id', $tenant->getKey())
            ->update(['is_enabled' => true]);

        $this->actingAs($reviewer);
        app(CurrentTenant::class)->set($tenant);
        Filament::setTenant($tenant);
        Filament::setCurrentPanel('admin');
    }
}
