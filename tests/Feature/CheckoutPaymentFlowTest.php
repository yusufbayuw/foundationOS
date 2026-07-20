<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Finance\Models\Payment;
use Modules\MerchOrder\Models\MerchOrder;
use Tests\TestCase;

class CheckoutPaymentFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_checkout_creates_pending_order_and_payment_transaction(): void
    {
        $this->bindGateway('gw-checkout-001');
        [$token, $tenant] = $this->makeTenantToken();

        $response = $this->withToken($token)->postJson('/api/v1/checkout/merch-orders', [
            'code' => 'MO-CHECKOUT-001',
            'name' => 'Uniform package checkout',
            'amount' => 250000,
            'customer' => ['email' => 'buyer@example.test'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.payment_reference', 'gw-checkout-001')
            ->assertJsonPath('data.status', 'pending_payment');

        $this->assertDatabaseHas('merch_orders', [
            'tenant_id' => $tenant->id,
            'code' => 'MO-CHECKOUT-001',
            'status' => 'pending_payment',
            'payment_reference' => 'gw-checkout-001',
        ]);
        $this->assertDatabaseHas('payments', [
            'tenant_id' => $tenant->id,
            'payment_reference' => 'gw-checkout-001',
            'status' => 'pending',
        ]);
    }

    public function test_paid_webhook_marks_order_paid_and_payment_verified(): void
    {
        $order = $this->createPendingCheckout('gw-paid-001');

        $response = $this->postJson('/api/v1/payments/webhook', [
            'payment_reference' => 'gw-paid-001',
            'status' => 'paid',
        ]);

        $response->assertOk()->assertJsonPath('data.status', 'paid');
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->paid_at);
        $this->assertSame('verified', Payment::withoutTenantScope()->where('payment_reference', 'gw-paid-001')->first()->status);
    }

    public function test_failed_webhook_marks_order_payment_failed_and_payment_failed(): void
    {
        $order = $this->createPendingCheckout('gw-failed-001');

        $response = $this->postJson('/api/v1/payments/webhook', [
            'payment_reference' => 'gw-failed-001',
            'status' => 'failed',
        ]);

        $response->assertOk()->assertJsonPath('data.status', 'payment_failed');
        $this->assertSame('payment_failed', $order->fresh()->status);
        $this->assertSame('failed', Payment::withoutTenantScope()->where('payment_reference', 'gw-failed-001')->first()->status);
    }

    public function test_paid_webhook_is_idempotent(): void
    {
        $order = $this->createPendingCheckout('gw-idempotent-001');

        $this->postJson('/api/v1/payments/webhook', [
            'payment_reference' => 'gw-idempotent-001',
            'status' => 'paid',
        ])->assertOk();
        $firstPaidAt = $order->fresh()->paid_at?->toIso8601String();

        $this->postJson('/api/v1/payments/webhook', [
            'payment_reference' => 'gw-idempotent-001',
            'status' => 'paid',
        ])->assertOk();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame($firstPaidAt, $order->fresh()->paid_at?->toIso8601String());
        $this->assertSame(1, Payment::withoutTenantScope()->where('payment_reference', 'gw-idempotent-001')->count());
    }

    public function test_admin_can_mark_paid_order_ready_for_pickup(): void
    {
        $order = $this->createPendingCheckout('gw-ready-001');
        $this->postJson('/api/v1/payments/webhook', ['payment_reference' => 'gw-ready-001', 'status' => 'paid'])->assertOk();

        $this->assertTrue($order->fresh()->markReadyForPickup());
        $this->assertSame('ready_for_pickup', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->ready_for_pickup_at);
    }

    private function createPendingCheckout(string $reference): MerchOrder
    {
        $this->bindGateway($reference);
        [$token] = $this->makeTenantToken($reference);

        $this->withToken($token)->postJson('/api/v1/checkout/merch-orders', [
            'code' => 'MO-'.$reference,
            'name' => 'Checkout '.$reference,
            'amount' => 125000,
        ])->assertCreated();

        return MerchOrder::withoutTenantScope()->where('payment_reference', $reference)->firstOrFail();
    }

    private function bindGateway(string $reference): void
    {
        $this->app->bind(PaymentGateway::class, fn () => new class($reference) implements PaymentGateway
        {
            public function __construct(private readonly string $reference) {}

            public function createOrderTransaction($order, array $options): array
            {
                return [
                    'reference' => $this->reference,
                    'status' => 'pending',
                    'redirect_url' => 'https://pay.example.test/'.$this->reference,
                    'payload' => ['amount' => $options['amount']],
                ];
            }
        });
    }

    /** @return array{string, Tenant} */
    private function makeTenantToken(?string $suffix = null): array
    {
        $suffix ??= Str::lower(Str::random(6));
        $user = User::query()->create([
            'name' => 'Checkout User '.$suffix,
            'email' => 'checkout-'.$suffix.'@example.test',
            'password' => bcrypt('password'),
        ]);
        $plan = SubscriptionPlan::query()->create([
            'code' => 'checkout-plan-'.$suffix,
            'name' => 'Checkout Plan '.$suffix,
            'included_modules' => ['core', 'merchorder'],
        ]);
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'checkout-'.$suffix,
            'name' => 'Checkout Tenant '.$suffix,
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);
        $token = $user->createToken('checkout-test');
        PersonalAccessToken::find($token->accessToken->id)->update(['tenant_id' => $tenant->id]);

        return [$token->plainTextToken, $tenant];
    }
}
