<?php

namespace Tests\Feature;

use App\Filament\Pages\BillingPage;
use App\Services\BillingService;
use App\Support\CurrentTenant;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class BillingPageActionsTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        Filament::setTenant(null);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_generate_invoice_action_creates_invoice(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapBillingPageTenant();

        Livewire::actingAs($user)
            ->test(BillingPage::class)
            ->callAction('generateInvoice')
            ->assertNotified();

        $this->assertDatabaseHas(SubscriptionLog::class, [
            'tenant_id' => $tenant->id,
            'action' => 'monthly_invoice',
            'payment_status' => 'pending',
            'amount' => '125000.00',
        ]);
    }

    public function test_generate_invoice_action_warns_when_current_month_is_already_billed(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapBillingPageTenant();

        $this->makeInvoice($tenant, [
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        Livewire::actingAs($user)
            ->test(BillingPage::class)
            ->callAction('generateInvoice')
            ->assertNotified();

        $this->assertSame(1, SubscriptionLog::query()
            ->where('tenant_id', $tenant->id)
            ->where('action', 'monthly_invoice')
            ->whereYear('period_start', now()->year)
            ->whereMonth('period_start', now()->month)
            ->count());
    }

    public function test_pay_invoice_action_opens_midtrans_snap_event(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapBillingPageTenant();
        $invoice = $this->makeInvoice($tenant);

        $billing = Mockery::mock(BillingService::class);
        $billing->shouldReceive('calculateMonthlyAmount')
            ->with(Mockery::on(fn (Tenant $argument): bool => $argument->is($tenant)))
            ->andReturn($this->monthlyAmounts());
        $billing->shouldReceive('createSnapPayment')
            ->once()
            ->with(
                Mockery::on(fn (Tenant $argument): bool => $argument->is($tenant)),
                Mockery::on(fn (SubscriptionLog $argument): bool => $argument->is($invoice)),
            )
            ->andReturn('snap-token-123');
        $this->app->instance(BillingService::class, $billing);

        Livewire::actingAs($user)
            ->test(BillingPage::class)
            ->callAction('payInvoice', arguments: ['invoice' => $invoice->id])
            ->assertDispatched('open-midtrans-snap', token: 'snap-token-123')
            ->assertNotified();
    }

    public function test_pay_invoice_action_rejects_invoice_id_from_another_tenant(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapBillingPageTenant();
        ['tenant' => $otherTenant] = $this->makeTenantContext(['core'], 'other-billing-plan');
        $otherInvoice = $this->makeInvoice($otherTenant);

        $billing = Mockery::mock(BillingService::class);
        $billing->shouldReceive('calculateMonthlyAmount')
            ->with(Mockery::on(fn (Tenant $argument): bool => $argument->is($tenant)))
            ->andReturn($this->monthlyAmounts());
        $billing->shouldNotReceive('createSnapPayment');
        $this->app->instance(BillingService::class, $billing);

        Livewire::actingAs($user)
            ->test(BillingPage::class)
            ->callAction('payInvoice', arguments: ['invoice' => $otherInvoice->id])
            ->assertNotDispatched('open-midtrans-snap')
            ->assertNotified();
    }

    /**
     * @return array{tenant: Tenant, user: User}
     */
    private function bootstrapBillingPageTenant(): array
    {
        ['tenant' => $tenant, 'user' => $user] = $this->makeTenantContext(['core'], 'billing-page-plan');

        $tenant->subscriptionPlan->update([
            'price_monthly' => 125000,
            'price_yearly' => 1200000,
            'price_per_seat' => 0,
            'price_per_module' => 0,
            'free_seats' => 99,
            'free_modules' => 99,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel('admin');
        Filament::setTenant($tenant);
        app(CurrentTenant::class)->set($tenant);

        return ['tenant' => $tenant, 'user' => $user];
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeInvoice(Tenant $tenant, array $overrides = []): SubscriptionLog
    {
        return SubscriptionLog::query()->create(array_merge([
            'tenant_id' => $tenant->id,
            'action' => 'monthly_invoice',
            'new_plan_id' => $tenant->subscription_plan_id,
            'amount' => 125000,
            'currency' => 'IDR',
            'payment_status' => 'pending',
            'invoice_number' => 'INV-TEST-'.$tenant->id.'-'.SubscriptionLog::query()->count(),
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'metadata' => [],
        ], $overrides));
    }

    /**
     * @return array{base: int, seats: int, modules: int, total: int, active_seats: int, active_modules: int}
     */
    private function monthlyAmounts(): array
    {
        return [
            'base' => 125000,
            'seats' => 0,
            'modules' => 0,
            'total' => 125000,
            'active_seats' => 1,
            'active_modules' => 0,
        ];
    }
}
