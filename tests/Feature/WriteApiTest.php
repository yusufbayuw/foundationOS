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
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\LeaveRequest;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WriteApiTest extends TestCase
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
            'code' => 'write-api-plan',
            'name' => 'Write API Plan',
            'included_modules' => ['core', 'enrollment', 'employee', 'finance'],
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

        setPermissionsTeamId($this->tenant->id);
        Permission::findOrCreate('Create:Applicant', 'web');
        Permission::findOrCreate('Create:LeaveRequest', 'web');
        Permission::findOrCreate('Create:Payment', 'web');
        $this->user->givePermissionTo(['Create:Applicant', 'Create:LeaveRequest', 'Create:Payment']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

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

    #[DataProvider('leaveTenantIsolationCases')]
    public function test_post_leave_requests_rejects_employee_references_from_another_tenant(
        string $endpoint,
        string $foreignKey,
    ): void {
        ['tenant' => $otherTenant, 'organization' => $otherOrganization] = $this->otherTenantContext('LEAVE');
        $localEmployee = $this->employeeFor($this->tenant, $this->organization, 'LOCAL');
        $foreignEmployee = $this->employeeFor($otherTenant, $otherOrganization, 'FOREIGN');

        $payload = [
            'employee_id' => $localEmployee->getKey(),
            'leave_type' => 'annual',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-03',
            'total_days' => 3,
            'reason' => 'Tenant isolation test',
        ];
        $payload[$foreignKey] = $foreignEmployee->getKey();

        $this->withToken($this->token)
            ->postJson($endpoint, $payload)
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonPath("error.details.{$foreignKey}.0", 'The selected '.str_replace('_', ' ', $foreignKey).' is invalid.');

        $this->assertSame(0, LeaveRequest::withoutTenantScope()->count());
    }

    #[DataProvider('paymentEndpoints')]
    public function test_post_payments_accepts_invoice_and_account_from_the_active_tenant(string $endpoint): void
    {
        $invoice = $this->invoiceFor($this->tenant, $this->user, 'LOCAL');
        $account = $this->accountFor($this->tenant, $this->organization, 'LOCAL');

        $this->withToken($this->token)
            ->postJson($endpoint, $this->paymentPayload($invoice, $account, 'PAY-LOCAL'))
            ->assertCreated()
            ->assertJsonPath('data.payment_number', 'PAY-LOCAL');

        $payment = Payment::withoutTenantScope()->sole();

        $this->assertSame($this->tenant->getKey(), $payment->tenant_id);
        $this->assertSame($invoice->getKey(), $payment->student_invoice_id);
        $this->assertSame($account->getKey(), $payment->chart_of_account_id);
    }

    #[DataProvider('paymentEndpoints')]
    public function test_post_payments_rejects_an_invoice_from_another_tenant(string $endpoint): void
    {
        ['tenant' => $otherTenant] = $this->otherTenantContext('INVOICE');
        $foreignInvoice = $this->invoiceFor($otherTenant, $this->user, 'FOREIGN');
        $localAccount = $this->accountFor($this->tenant, $this->organization, 'LOCAL');

        $this->withToken($this->token)
            ->postJson($endpoint, $this->paymentPayload($foreignInvoice, $localAccount, 'PAY-FOREIGN-INVOICE'))
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonPath('error.details.student_invoice_id.0', 'The selected student invoice id is invalid.');

        $this->assertSame(0, Payment::withoutTenantScope()->count());
    }

    #[DataProvider('paymentEndpoints')]
    public function test_post_payments_rejects_an_account_from_another_tenant(string $endpoint): void
    {
        ['tenant' => $otherTenant, 'organization' => $otherOrganization] = $this->otherTenantContext('ACCOUNT');
        $localInvoice = $this->invoiceFor($this->tenant, $this->user, 'LOCAL');
        $foreignAccount = $this->accountFor($otherTenant, $otherOrganization, 'FOREIGN');

        $this->withToken($this->token)
            ->postJson($endpoint, $this->paymentPayload($localInvoice, $foreignAccount, 'PAY-FOREIGN-ACCOUNT'))
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonPath('error.details.chart_of_account_id.0', 'The selected chart of account id is invalid.');

        $this->assertSame(0, Payment::withoutTenantScope()->count());
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

    /**
     * @return array<string, array{string}>
     */
    public static function paymentEndpoints(): array
    {
        return [
            'API v1' => ['/api/v1/payments'],
            'API v2' => ['/api/v2/payments'],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function leaveTenantIsolationCases(): array
    {
        return [
            'API v1 primary employee' => ['/api/v1/leave-requests', 'employee_id'],
            'API v1 substitute employee' => ['/api/v1/leave-requests', 'substitute_employee_id'],
            'API v2 primary employee' => ['/api/v2/leave-requests', 'employee_id'],
            'API v2 substitute employee' => ['/api/v2/leave-requests', 'substitute_employee_id'],
        ];
    }

    private function employeeFor(Tenant $tenant, Organization $organization, string $suffix): Employee
    {
        return Employee::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'organization_id' => $organization->getKey(),
            'user_id' => $this->user->getKey(),
            'employee_number' => "EMP-{$suffix}",
            'full_name' => "Employee {$suffix}",
            'join_date' => '2024-01-01',
        ]);
    }

    private function invoiceFor(Tenant $tenant, User $invoiceable, string $suffix): StudentInvoice
    {
        return StudentInvoice::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'invoice_number' => "INV-{$suffix}",
            'issue_date' => '2026-08-01',
            'due_date' => '2026-08-31',
            'amount' => 250000,
            'total_amount' => 250000,
            'remaining_amount' => 250000,
            'invoiceable_type' => $invoiceable->getMorphClass(),
            'invoiceable_id' => $invoiceable->getKey(),
            'status' => 'issued',
        ]);
    }

    private function accountFor(Tenant $tenant, Organization $organization, string $suffix): ChartOfAccount
    {
        return ChartOfAccount::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'organization_id' => $organization->getKey(),
            'code' => "CASH-{$suffix}",
            'name' => "Cash {$suffix}",
            'type' => 'asset',
            'normal_balance' => 'debit',
        ]);
    }

    /**
     * @return array{tenant: Tenant, organization: Organization}
     */
    private function otherTenantContext(string $suffix): array
    {
        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-other-'.Str::lower($suffix),
            'name' => "Other Tenant {$suffix}",
            'subscription_plan_id' => $this->tenant->subscription_plan_id,
            'created_by' => $this->user->getKey(),
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->getKey(),
            'name' => "Other Organization {$suffix}",
            'code' => "OTHER-{$suffix}",
            'is_active' => true,
            'is_main' => true,
        ]);

        return compact('tenant', 'organization');
    }

    /**
     * @return array<string, int|string>
     */
    private function paymentPayload(StudentInvoice $invoice, ChartOfAccount $account, string $number): array
    {
        return [
            'student_invoice_id' => $invoice->getKey(),
            'chart_of_account_id' => $account->getKey(),
            'payment_number' => $number,
            'payment_date' => '2026-08-25',
            'amount' => 250000,
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
        ];
    }
}
