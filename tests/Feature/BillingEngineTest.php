<?php

namespace Tests\Feature;

use App\Services\BillingService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
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

        $billing->handleWebhookNotification([
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
        ]);

        $invoice->refresh();
        $this->tenant->refresh();

        $this->assertEquals('paid', $invoice->payment_status);
        $this->assertEquals('bank_transfer', $invoice->payment_method);
        $this->assertEquals('active', $this->tenant->status);
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
}
