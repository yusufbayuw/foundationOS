<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Alumni\Models\JobPosting;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\TestCase;

class JobPostingApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private Organization $organization;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'jobs-api-plan',
            'name' => 'Jobs API Plan',
            'included_modules' => ['core', 'alumni'],
        ]);

        $user = User::create([
            'name' => 'Jobs API User',
            'email' => 'jobs-api@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-jobs',
            'name' => 'Jobs Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $this->organization = Organization::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Jobs School',
            'code' => 'JOB-SCHOOL',
            'is_active' => true,
            'is_main' => true,
        ]);

        $createdToken = $user->createToken('jobs-token');
        $pat = PersonalAccessToken::find($createdToken->accessToken->id);
        $pat->update(['tenant_id' => $this->tenant->id]);
        $this->token = $createdToken->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_jobs_index_returns_active_jobs_only(): void
    {
        $activeJob = $this->createJobPosting(['role_title' => 'Backend Engineer']);
        $this->createJobPosting(['status' => 'draft', 'role_title' => 'Hidden Draft']);

        $response = $this->withToken($this->token)->getJson('/api/v1/app/jobs');

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($activeJob->id));
        $this->assertCount(1, $ids);
    }

    public function test_jobs_index_hides_expired_jobs(): void
    {
        $activeJob = $this->createJobPosting(['role_title' => 'Open Role']);
        $this->createJobPosting([
            'role_title' => 'Expired Role',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/app/jobs');

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($activeJob->id));
        $this->assertCount(1, $ids);
    }

    public function test_jobs_show_returns_application_method_details(): void
    {
        $jobPosting = $this->createJobPosting([
            'application_method' => 'email',
            'application_url' => null,
            'application_email' => 'careers@example.com',
        ]);

        $response = $this->withToken($this->token)->getJson("/api/v1/app/jobs/{$jobPosting->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $jobPosting->id)
            ->assertJsonPath('data.application_method', 'email')
            ->assertJsonPath('data.application_email', 'careers@example.com')
            ->assertJsonPath('data.application_url', null);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createJobPosting(array $overrides = []): JobPosting
    {
        return JobPosting::create(array_merge([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'code' => 'JOB-'.Str::upper(Str::random(6)),
            'name' => 'Software Engineer',
            'status' => 'active',
            'description' => 'Build learning software.',
            'company' => 'Example Corp',
            'role_title' => 'Software Engineer',
            'location' => 'Jakarta',
            'employment_type' => 'full_time',
            'application_method' => 'external_url',
            'application_url' => 'https://example.com/jobs/software-engineer',
            'application_email' => null,
            'expires_at' => now()->addWeek(),
            'meta' => [],
        ], $overrides));
    }
}
