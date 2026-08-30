<?php

namespace Tests\Feature;

use App\Filament\Pages\BillingPage;
use App\Jobs\CreateBillingSnapPaymentJob;
use App\Models\Role;
use App\Services\BillingService;
use App\Support\CurrentTenant;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BillingEngineTest extends TestCase
{
    use LazilyRefreshDatabase;

    private SubscriptionPlan $plan;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = SubscriptionPlan::create([
            'code' => 'starter',
            'name' => 'Starter Plan',
            'price_monthly' => 100000,
            'price_yearly' => 1000000,
            'price_per_seat' => 25000,
            'price_per_module' => 15000,
            'free_seats' => 2,
            'free_modules' => 1,
            'grace_period_days' => 7,
            'max_users' => 50,
            'max_organizations' => 3,
            'max_storage_gb' => 5,
            'included_modules' => ['core'],
            'is_active' => true,
        ]);

        $this->tenant = Tenant::create([
            'uuid' => Str::uuid(),
            'code' => 'test_org',
            'name' => 'Test Organization',
            'status' => 'active',
            'subscription_plan_id' => $this->plan->id,
            'subscription_expires_at' => now()->addDays(30),
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
        ]);
    }

    private function attachUsersToTenant(Tenant $tenant, int $count): void
    {
        $role = TenantRole::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'member'],
            ['name' => 'Member', 'permissions' => [], 'is_super_admin' => false]
        );

        $users = User::factory()->count($count)->create();

        $rows = $users->map(fn (User $u) => [
            'user_id' => $u->id,
            'tenant_id' => $tenant->id,
            'tenant_role_id' => $role->id,
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        DB::table('user_tenant_roles')->insert($rows);
    }

    private function createPendingInvoice(array $attributes = []): SubscriptionLog
    {
        return SubscriptionLog::query()->create(array_merge([
            'tenant_id' => $this->tenant->id,
            'action' => 'monthly_invoice',
            'new_plan_id' => $this->plan->id,
            'amount' => 100000,
            'currency' => 'IDR',
            'payment_status' => 'pending',
            'invoice_number' => 'INV-TEST-'.Str::upper(Str::random(8)),
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'metadata' => [],
        ], $attributes));
    }

    private function actingAsBillingUser(): User
    {
        $user = User::factory()->create();
        $role = TenantRole::firstOrCreate(
            ['tenant_id' => $this->tenant->id, 'slug' => 'member'],
            ['name' => 'Member', 'permissions' => [], 'is_super_admin' => false]
        );

        DB::table('user_tenant_roles')->insert([
            'user_id' => $user->id,
            'tenant_id' => $this->tenant->id,
            'tenant_role_id' => $role->id,
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        setPermissionsTeamId($this->tenant->getKey());
        $pagePermission = Permission::findOrCreate('View:BillingPage', 'web');
        $paymentPermission = Permission::findOrCreate('Update:SubscriptionLog', 'web');
        $billingManager = Role::firstOrCreate([
            'name' => 'billing_manager',
            'guard_name' => 'web',
            'tenant_id' => $this->tenant->getKey(),
        ]);
        $billingManager->givePermissionTo([$pagePermission, $paymentPermission]);
        $user->roles()->syncWithoutDetaching([
            $billingManager->getKey() => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $this->tenant->getKey(),
            ],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $user->unsetRelation('roles')->unsetRelation('permissions');

        Filament::setCurrentPanel('admin');
        $this->actingAs($user);
        app(CurrentTenant::class)->set($this->tenant);
        Filament::setTenant($this->tenant);

        return $user;
    }

    protected function tearDown(): void
    {
        Filament::setTenant(null);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_calculate_monthly_amount_with_base_plan_only(): void
    {
        // 2 users (free_seats=2) → no seat charge
        $this->attachUsersToTenant($this->tenant, 2);

        $billing = app(BillingService::class);
        $amounts = $billing->calculateMonthlyAmount($this->tenant);

        $this->assertEquals(100000, $amounts['base']);
        $this->assertEquals(0, $amounts['seats']);
        $this->assertEquals(0, $amounts['modules']);
        $this->assertEquals(100000, $amounts['total']);
    }

    public function test_calculate_monthly_amount_with_extra_seats(): void
    {
        // 5 users: 2 free + 3 billable at 25000 each = 75000 seat charge
        $this->attachUsersToTenant($this->tenant, 5);

        $billing = app(BillingService::class);
        $amounts = $billing->calculateMonthlyAmount($this->tenant);

        $this->assertEquals(100000, $amounts['base']);
        $this->assertEquals(75000, $amounts['seats']);
        $this->assertEquals(0, $amounts['modules']);
        $this->assertEquals(175000, $amounts['total']);
    }

    public function test_generate_invoice_creates_subscription_log(): void
    {
        $billing = app(BillingService::class);
        $invoice = $billing->generateInvoice($this->tenant);

        $this->assertInstanceOf(SubscriptionLog::class, $invoice);
        $this->assertEquals($this->tenant->id, $invoice->tenant_id);
        $this->assertEquals('monthly_invoice', $invoice->action);
        $this->assertEquals('pending', $invoice->payment_status);
        $this->assertStringStartsWith('INV-TEST_ORG-', $invoice->invoice_number);
        $this->assertNotNull($invoice->period_start);
        $this->assertNotNull($invoice->period_end);
    }

    public function test_tenant_is_active_when_subscription_not_expired(): void
    {
        $this->assertTrue($this->tenant->isSubscriptionActive());
        $this->assertFalse($this->tenant->isLocked());
    }

    public function test_tenant_enters_grace_period_when_past_due(): void
    {
        $this->tenant->update([
            'status' => 'past_due',
            'grace_period_ends_at' => now()->addDays(7),
        ]);

        $this->assertTrue($this->tenant->fresh()->isInGracePeriod());
        $this->assertFalse($this->tenant->fresh()->isLocked());
    }

    public function test_tenant_is_locked_after_grace_period_expires(): void
    {
        $this->tenant->update([
            'status' => 'past_due',
            'grace_period_ends_at' => now()->subDay(),
        ]);

        $this->assertFalse($this->tenant->fresh()->isInGracePeriod());
        $this->assertTrue($this->tenant->fresh()->isLocked());
    }

    public function test_webhook_marks_invoice_paid_and_activates_tenant(): void
    {
        config(['midtrans.server_key' => 'billing-test-key']);

        $this->tenant->update(['status' => 'past_due']);
        $billing = app(BillingService::class);
        $invoice = $billing->generateInvoice($this->tenant);
        $grossAmount = number_format((float) $invoice->amount, 2, '.', '');

        $notification = [
            'order_id' => $invoice->invoice_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'currency' => $invoice->currency,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'txn-123',
            'signature_key' => hash(
                'sha512',
                $invoice->invoice_number.'200'.$grossAmount.config('midtrans.server_key'),
            ),
        ];

        $billing->handleWebhookNotification($notification);
        $billing->handleWebhookNotification($notification);

        $invoice->refresh();
        $this->tenant->refresh();

        $this->assertEquals('paid', $invoice->payment_status);
        $this->assertEquals('bank_transfer', $invoice->payment_method);
        $this->assertEquals('active', $this->tenant->status);
        $this->assertSame('subscription_log', $invoice->getMorphClass());
        $this->assertSame(1, Activity::query()
            ->where('subject_type', 'subscription_log')
            ->where('subject_id', $invoice->getKey())
            ->where('event', 'updated')
            ->count());
    }

    public function test_subscription_plan_calculates_correct_monthly_amount(): void
    {
        // 4 seats - 2 free = 2 × 25000 = 50000
        // 3 modules - 1 free = 2 × 15000 = 30000
        // base = 100000 → total = 180000
        $amount = $this->plan->calculateMonthlyAmount(4, 3);

        $this->assertEquals(180000.0, $amount);
    }

    public function test_check_grace_period_command_suspends_overdue_tenants(): void
    {
        $this->tenant->update([
            'status' => 'past_due',
            'grace_period_ends_at' => now()->subDay(),
        ]);

        $this->artisan('fos:billing:check-grace-period')
            ->assertSuccessful();

        $this->assertEquals('suspended', $this->tenant->fresh()->status);
    }

    public function test_generate_invoices_command_skips_tenant_with_no_plan(): void
    {
        $freeTenant = Tenant::create([
            'uuid' => Str::uuid(),
            'code' => 'free_org',
            'name' => 'Free Org',
            'status' => 'active',
            'subscription_plan_id' => null,
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
        ]);

        $this->artisan('fos:billing:generate-invoices --tenant='.$freeTenant->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('subscription_logs', [
            'tenant_id' => $freeTenant->id,
            'action' => 'monthly_invoice',
        ]);
    }

    public function test_pay_invoice_dispatches_snap_payment_job(): void
    {
        Bus::fake();
        $user = $this->actingAsBillingUser();
        $invoice = $this->createPendingInvoice();

        Livewire::test(BillingPage::class)
            ->call('payInvoice', $invoice->id);

        Bus::assertDispatched(CreateBillingSnapPaymentJob::class, fn (CreateBillingSnapPaymentJob $job): bool => $job->tenantId === $this->tenant->id
            && $job->subscriptionLogId === $invoice->id
            && $job->actorId === $user->id
            && $job->uniqueId() === "billing-snap:{$this->tenant->id}:{$invoice->id}");

        $this->assertSame('queued', $invoice->fresh()->metadata['payment_session_status']);
    }

    public function test_snap_payment_job_stores_ready_payment_session(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createPendingInvoice();

        $this->mock(BillingService::class, function ($mock) use ($invoice, $user): void {
            $mock->shouldReceive('createSnapPayment')
                ->once()
                ->andReturnUsing(function (Tenant $tenant, SubscriptionLog $subscriptionLog, ?int $actorId) use ($invoice, $user): string {
                    $this->assertSame($this->tenant->id, $tenant->id);
                    $this->assertSame($invoice->id, $subscriptionLog->id);
                    $this->assertSame($user->id, $actorId);

                    $subscriptionLog->update([
                        'invoice_url' => 'https://app.midtrans.com/snap/v2/vtweb/snap-token-123',
                        'processed_by' => $actorId,
                        'metadata' => array_merge($subscriptionLog->metadata ?? [], [
                            'snap_token' => 'snap-token-123',
                            'payment_session_status' => 'ready',
                        ]),
                    ]);

                    return 'snap-token-123';
                });
        });

        new CreateBillingSnapPaymentJob($this->tenant->id, $invoice->id, $user->id)
            ->handle(app(BillingService::class));

        $invoice->refresh();

        $this->assertSame('https://app.midtrans.com/snap/v2/vtweb/snap-token-123', $invoice->invoice_url);
        $this->assertSame('snap-token-123', $invoice->metadata['snap_token']);
        $this->assertSame('ready', $invoice->metadata['payment_session_status']);
        $this->assertSame($user->id, $invoice->processed_by);
    }

    public function test_snap_payment_job_records_gateway_failure(): void
    {
        $invoice = $this->createPendingInvoice();

        $this->mock(BillingService::class, function ($mock): void {
            $mock->shouldReceive('createSnapPayment')
                ->once()
                ->andThrow(new Exception('Midtrans timeout'));
        });

        try {
            new CreateBillingSnapPaymentJob($this->tenant->id, $invoice->id, null)
                ->handle(app(BillingService::class));

            $this->fail('Expected the payment gateway exception to be rethrown.');
        } catch (Exception $exception) {
            $this->assertSame('Midtrans timeout', $exception->getMessage());
        }

        $this->assertSame('failed', $invoice->fresh()->metadata['payment_session_status']);
        $this->assertSame('Midtrans timeout', $invoice->fresh()->metadata['payment_session_error']);
    }

    public function test_snap_payment_job_final_failure_callback_persists_status(): void
    {
        $invoice = $this->createPendingInvoice();
        $job = new CreateBillingSnapPaymentJob($this->tenant->id, $invoice->id, null);

        $job->failed(new Exception('Midtrans retries exhausted'));

        $this->assertSame('failed', $invoice->fresh()->metadata['payment_session_status']);
        $this->assertSame('Midtrans retries exhausted', $invoice->fresh()->metadata['payment_session_error']);
    }

    public function test_snap_payment_job_is_idempotent_for_existing_payment_session(): void
    {
        $invoice = $this->createPendingInvoice([
            'invoice_url' => 'https://app.midtrans.com/snap/v2/vtweb/existing-token',
            'metadata' => [
                'snap_token' => 'existing-token',
                'payment_session_status' => 'ready',
            ],
        ]);

        $this->mock(BillingService::class, function ($mock): void {
            $mock->shouldNotReceive('createSnapPayment');
        });

        new CreateBillingSnapPaymentJob($this->tenant->id, $invoice->id, null)
            ->handle(app(BillingService::class));

        $this->assertSame('existing-token', $invoice->fresh()->metadata['snap_token']);
    }
}
