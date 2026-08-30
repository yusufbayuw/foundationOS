<?php

namespace Tests\Feature;

use App\Enums\ShopOrderStatus;
use App\Models\PersonalAccessToken;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Finance\Models\Payment;
use Modules\MerchOrder\Models\MerchOrder;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CheckoutPaymentFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'midtrans.server_key' => 'checkout-webhook-secret',
            'midtrans.currency' => 'IDR',
        ]);
    }

    public function test_checkout_creates_pending_order_and_payment_transaction(): void
    {
        $this->bindGateway('gw-checkout-001');
        [$token, $tenant] = $this->makeTenantToken();
        $organization = $this->organizationFor($tenant, 'CHECKOUT');

        $response = $this->withToken($token)->postJson('/api/v1/checkout/merch-orders', [
            'organization_id' => $organization->getKey(),
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
            'organization_id' => $organization->getKey(),
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

    #[DataProvider('checkoutEndpoints')]
    public function test_checkout_rejects_an_organization_from_another_tenant(string $endpoint): void
    {
        $this->bindGateway('gw-cross-tenant');
        [$token] = $this->makeTenantToken('checkout-primary');
        [, $otherTenant] = $this->makeTenantToken('checkout-foreign');
        $foreignOrganization = $this->organizationFor($otherTenant, 'FOREIGN');

        $this->withToken($token)->postJson($endpoint, [
            'organization_id' => $foreignOrganization->getKey(),
            'code' => 'MO-CROSS-TENANT',
            'name' => 'Cross tenant checkout',
            'amount' => 250000,
        ])->assertUnprocessable()
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonPath('error.details.organization_id.0', 'The selected organization id is invalid.');

        $this->assertSame(0, MerchOrder::withoutTenantScope()->count());
        $this->assertSame(0, Payment::withoutTenantScope()->count());
    }

    #[DataProvider('checkoutEndpoints')]
    public function test_checkout_requires_the_shield_create_permission(string $endpoint): void
    {
        $this->bindGateway('gw-forbidden');
        [$token] = $this->makeTenantToken('checkout-forbidden', grantPermission: false);

        $this->withToken($token)->postJson($endpoint, [
            'code' => 'MO-FORBIDDEN',
            'name' => 'Unauthorized checkout',
            'amount' => 250000,
        ])->assertForbidden();

        $this->assertSame(0, MerchOrder::withoutTenantScope()->count());
        $this->assertSame(0, Payment::withoutTenantScope()->count());
    }

    public function test_paid_webhook_marks_order_paid_and_payment_verified(): void
    {
        $order = $this->createPendingCheckout('gw-paid-001');

        $response = $this->postJson(
            '/api/v1/payments/webhook',
            $this->webhookPayload('gw-paid-001'),
        );

        $response->assertOk()->assertJsonPath('data.status', 'paid');
        $this->assertSame(ShopOrderStatus::Paid, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->paid_at);
        $this->assertSame('verified', Payment::withoutTenantScope()->where('payment_reference', 'gw-paid-001')->first()->status);
    }

    public function test_failed_webhook_marks_order_payment_failed_and_payment_failed(): void
    {
        $order = $this->createPendingCheckout('gw-failed-001');

        $response = $this->postJson(
            '/api/v1/payments/webhook',
            $this->webhookPayload('gw-failed-001', ['transaction_status' => 'deny']),
        );

        $response->assertOk()->assertJsonPath('data.status', 'payment_failed');
        $this->assertSame(ShopOrderStatus::PaymentFailed, $order->fresh()->status);
        $this->assertSame('failed', Payment::withoutTenantScope()->where('payment_reference', 'gw-failed-001')->first()->status);
    }

    public function test_paid_webhook_is_idempotent(): void
    {
        $order = $this->createPendingCheckout('gw-idempotent-001');
        $payload = $this->webhookPayload('gw-idempotent-001');

        $this->postJson('/api/v1/payments/webhook', $payload)->assertOk();
        $firstPaidAt = $order->fresh()->paid_at?->toIso8601String();

        $this->postJson('/api/v1/payments/webhook', $payload)->assertOk();

        $this->assertSame(ShopOrderStatus::Paid, $order->fresh()->status);
        $this->assertSame($firstPaidAt, $order->fresh()->paid_at?->toIso8601String());
        $this->assertSame(1, Payment::withoutTenantScope()->where('payment_reference', 'gw-idempotent-001')->count());
    }

    public function test_admin_can_mark_paid_order_ready_for_pickup(): void
    {
        $order = $this->createPendingCheckout('gw-ready-001');
        $this->postJson('/api/v1/payments/webhook', $this->webhookPayload('gw-ready-001'))->assertOk();

        $this->assertTrue($order->fresh()->markReadyForPickup());
        $this->assertSame(ShopOrderStatus::ReadyForPickup, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->ready_for_pickup_at);
    }

    public function test_webhook_rejects_an_invalid_signature_without_mutating_payment(): void
    {
        $order = $this->createPendingCheckout('gw-invalid-signature');
        $payload = $this->webhookPayload('gw-invalid-signature');
        $payload['signature_key'] = str_repeat('0', 128);

        $this->postJson('/api/v1/payments/webhook', $payload)
            ->assertBadRequest()
            ->assertJsonPath('error.code', 'invalid_payment_webhook');

        $this->assertSame(ShopOrderStatus::PendingPayment, $order->fresh()->status);
        $this->assertSame('pending', Payment::withoutTenantScope()->where('payment_reference', 'gw-invalid-signature')->firstOrFail()->status);
    }

    public function test_webhook_rejects_an_amount_mismatch_without_mutating_payment(): void
    {
        $order = $this->createPendingCheckout('gw-amount-mismatch');

        $this->postJson('/api/v1/payments/webhook', $this->webhookPayload('gw-amount-mismatch', [
            'gross_amount' => '125001.00',
        ]))->assertBadRequest()
            ->assertJsonPath('error.code', 'payment_amount_mismatch');

        $this->assertSame(ShopOrderStatus::PendingPayment, $order->fresh()->status);
        $this->assertSame('pending', Payment::withoutTenantScope()->where('payment_reference', 'gw-amount-mismatch')->firstOrFail()->status);
    }

    public function test_webhook_rejects_a_currency_mismatch_without_mutating_payment(): void
    {
        $order = $this->createPendingCheckout('gw-currency-mismatch');

        $this->postJson('/api/v1/payments/webhook', $this->webhookPayload('gw-currency-mismatch', [
            'currency' => 'USD',
        ]))->assertBadRequest()
            ->assertJsonPath('error.code', 'payment_currency_mismatch');

        $this->assertSame(ShopOrderStatus::PendingPayment, $order->fresh()->status);
        $this->assertSame('pending', Payment::withoutTenantScope()->where('payment_reference', 'gw-currency-mismatch')->firstOrFail()->status);
    }

    public function test_webhook_fails_closed_when_the_server_key_is_not_configured(): void
    {
        $order = $this->createPendingCheckout('gw-missing-key');
        $payload = $this->webhookPayload('gw-missing-key');
        config(['midtrans.server_key' => '']);

        $this->postJson('/api/v1/payments/webhook', $payload)
            ->assertServiceUnavailable()
            ->assertJsonPath('error.code', 'payment_webhook_unavailable');

        $this->assertSame(ShopOrderStatus::PendingPayment, $order->fresh()->status);
        $this->assertSame('pending', Payment::withoutTenantScope()->where('payment_reference', 'gw-missing-key')->firstOrFail()->status);
    }

    public function test_payment_webhook_uses_the_named_webhook_rate_limiter(): void
    {
        $route = Route::getRoutes()->getByName('api.v1.payments.webhook');

        $this->assertNotNull($route);
        $this->assertContains('throttle:webhooks', $route->gatherMiddleware());
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
    private function makeTenantToken(?string $suffix = null, bool $grantPermission = true): array
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

        if ($grantPermission) {
            setPermissionsTeamId($tenant->getKey());
            Permission::findOrCreate('Create:MerchOrder', 'web');
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $user->givePermissionTo('Create:MerchOrder');
        }

        $token = $user->createToken('checkout-test');
        PersonalAccessToken::find($token->accessToken->id)->update(['tenant_id' => $tenant->id]);

        return [$token->plainTextToken, $tenant];
    }

    private function organizationFor(Tenant $tenant, string $suffix): Organization
    {
        return Organization::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => "ORG-{$suffix}",
            'name' => "Organization {$suffix}",
            'is_active' => true,
            'is_main' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function webhookPayload(string $reference, array $overrides = []): array
    {
        $payload = array_merge([
            'order_id' => $reference,
            'transaction_status' => 'settlement',
            'transaction_id' => 'txn-'.$reference,
            'status_code' => '200',
            'gross_amount' => '125000.00',
            'currency' => 'IDR',
            'fraud_status' => 'accept',
            'payment_type' => 'bank_transfer',
        ], $overrides);

        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key'),
        );

        return $payload;
    }

    /**
     * @return array<string, array{string}>
     */
    public static function checkoutEndpoints(): array
    {
        return [
            'API v1' => ['/api/v1/checkout/merch-orders'],
            'API v2' => ['/api/v2/checkout/merch-orders'],
        ];
    }
}
