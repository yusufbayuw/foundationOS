<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\TestCase;

class MemberRegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => bcrypt('password'),
        ]);

        $plan = SubscriptionPlan::create([
            'code' => 'member-plan',
            'name' => 'Member Plan',
            'included_modules' => ['core', 'member'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'member-tenant',
            'name' => 'Member Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        app(CurrentTenant::class)->set($tenant);

        $created = $user->createToken('member-token');
        PersonalAccessToken::find($created->accessToken->id)?->update(['tenant_id' => $tenant->id]);
        $this->token = $created->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_alumni_registration_stores_graduation_and_occupation_profile_data(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/members/register', [
            'member_type' => 'alumni',
            'profile' => [
                'graduation_year' => 2020,
                'occupation' => 'Software Engineer',
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.member_type', 'alumni')
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.profile.graduation_year', 2020)
            ->assertJsonPath('data.profile.occupation', 'Software Engineer');

        $this->assertDatabaseHas('members', ['status' => 'pending']);
        $this->assertDatabaseHas('member_profiles', [
            'profile_data->graduation_year' => 2020,
            'profile_data->occupation' => 'Software Engineer',
        ]);
    }

    public function test_teacher_and_employee_registration_validate_relevant_fields(): void
    {
        $teacherResponse = $this->withToken($this->token)->postJson('/api/v1/members/register', [
            'member_type' => 'teacher',
            'profile' => [
                'institution' => 'Foundation School',
                'teacher_number' => 'T-001',
            ],
        ]);

        $teacherResponse->assertCreated()
            ->assertJsonPath('data.member_type', 'teacher')
            ->assertJsonPath('data.profile.teacher_number', 'T-001');

        $employeeUser = User::create([
            'name' => 'Employee Member',
            'email' => 'employee-member@example.com',
            'password' => bcrypt('password'),
        ]);
        $created = $employeeUser->createToken('employee-member-token');
        $employeeToken = $created->plainTextToken;

        $employeeResponse = $this->withToken($employeeToken)->postJson('/api/v1/members/register', [
            'member_type' => 'employee',
            'profile' => [
                'institution' => 'Foundation Office',
                'position' => 'Finance Staff',
                'employee_number' => 'E-001',
            ],
        ]);

        $employeeResponse->assertCreated()
            ->assertJsonPath('data.member_type', 'employee')
            ->assertJsonPath('data.profile.position', 'Finance Staff');
    }

    public function test_valid_proof_upload_is_stored_on_private_disk(): void
    {
        Storage::fake('local');

        $response = $this->withToken($this->token)->post('/api/v1/members/register', [
            'member_type' => 'alumni',
            'profile' => [
                'graduation_year' => 2019,
                'occupation' => 'Teacher',
            ],
            'proof' => UploadedFile::fake()->create('proof.pdf', 128, 'application/pdf'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.proofs.0.disk', 'local')
            ->assertJsonPath('data.proofs.0.mime_type', 'application/pdf')
            ->assertJsonPath('data.proofs.0.status', 'pending');

        Storage::disk('local')->assertExists($response->json('data.proofs.0.file_path'));
    }

    public function test_invalid_proof_upload_is_rejected(): void
    {
        $response = $this->withToken($this->token)->post('/api/v1/members/register', [
            'member_type' => 'alumni',
            'profile' => [
                'graduation_year' => 2018,
                'occupation' => 'Entrepreneur',
            ],
            'proof' => UploadedFile::fake()->create('proof.exe', 12, 'application/x-msdownload'),
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'validation_failed');
    }
}
