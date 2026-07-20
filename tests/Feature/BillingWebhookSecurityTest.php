<?php

namespace Tests\Feature;

use App\Services\BillingService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Tests\TestCase;

class BillingWebhookSecurityTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private SubscriptionLog $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        config(['midtrans.server_key' => 'test-server-key']);

        $plan = SubscriptionPlan::query()->create([
            'code' => 'webhook-plan',
            'name' => 'Webhook Plan',
            'price_monthly' => 125000,
            'included_modules' => ['core'],
            'is_active' => true,
        ]);

        $this->tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'webhook-tenant',
            'name' => 'Webhook Tenant',
            'status' => 'past_due',
            'currency' => 'IDR',
            'subscription_plan_id' => $plan->getKey(),
        ]);

        $this->invoice = SubscriptionLog::query()->create([
            'tenant_id' => $this->tenant->getKey(),
            'action' => 'monthly_invoice',
            'new_plan_id' => $plan->getKey(),
            'amount' => 125000,
            'currency' => 'IDR',
            'payment_status' => 'pending',
            'invoice_number' => 'INV-WEBHOOK-001',
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'metadata' => ['source' => 'test'],
        ]);
    }

    public function test_paid_webhook_marks_invoice_paid_and_activates_tenant(): void
    {
        app(BillingService::class)->handleWebhookNotification($this->payload([
            'transaction_status' => 'settlement',
            'transaction_id' => 'txn-paid-001',
        ]));

        $this->assertSame('paid', $this->invoice->fresh()->payment_status);
        $this->assertSame('active', $this->tenant->fresh()->status);
        $this->assertSame('txn-paid-001', $this->invoice->fresh()->metadata['midtrans_transaction_id']);
        $this->assertSame('settlement', $this->invoice->fresh()->metadata['midtrans_status']);
        $this->assertCount(1, $this->invoice->fresh()->metadata['midtrans_webhooks']);
    }

    public function test_pending_webhook_keeps_invoice_pending_and_does_not_activate_tenant(): void
    {
        app(BillingService::class)->handleWebhookNotification($this->payload([
            'transaction_status' => 'pending',
            'transaction_id' => 'txn-pending-001',
        ]));

        $this->assertSame('pending', $this->invoice->fresh()->payment_status);
        $this->assertSame('past_due', $this->tenant->fresh()->status);
        $this->assertCount(1, $this->invoice->fresh()->metadata['midtrans_webhooks']);
    }

    public function test_failed_webhook_marks_invoice_failed_without_activating_tenant(): void
    {
        app(BillingService::class)->handleWebhookNotification($this->payload([
            'transaction_status' => 'expire',
            'transaction_id' => 'txn-failed-001',
        ]));

        $this->assertSame('failed', $this->invoice->fresh()->payment_status);
        $this->assertSame('past_due', $this->tenant->fresh()->status);
    }

    public function test_duplicate_paid_webhook_is_idempotent(): void
    {
        $payload = $this->payload([
            'transaction_status' => 'settlement',
            'transaction_id' => 'txn-duplicate-001',
        ]);

        app(BillingService::class)->handleWebhookNotification($payload);
        $firstPaidAt = $this->tenant->fresh()->subscribed_at?->toIso8601String();

        app(BillingService::class)->handleWebhookNotification($payload);

        $this->assertSame('paid', $this->invoice->fresh()->payment_status);
        $this->assertSame($firstPaidAt, $this->tenant->fresh()->subscribed_at?->toIso8601String());
        $this->assertCount(1, $this->invoice->fresh()->metadata['processed_midtrans_webhook_keys']);
        $this->assertCount(1, $this->invoice->fresh()->metadata['midtrans_webhooks']);
    }

    public function test_invalid_signature_is_rejected_without_changing_invoice_or_tenant(): void
    {
        $payload = $this->payload([
            'transaction_status' => 'settlement',
            'transaction_id' => 'txn-invalid-signature',
            'signature_key' => 'invalid-signature',
        ], sign: false);

        $response = $this->postJson(route('billing.webhook'), $payload);

        $response->assertStatus(400)
            ->assertJson(['status' => 'error', 'message' => 'Invalid webhook notification.']);

        $this->assertSame('pending', $this->invoice->fresh()->payment_status);
        $this->assertSame('past_due', $this->tenant->fresh()->status);
    }

    public function test_missing_signature_is_rejected_without_changing_invoice_or_tenant(): void
    {
        $payload = $this->payload([
            'transaction_status' => 'settlement',
            'transaction_id' => 'txn-missing-signature',
        ], sign: false);

        $response = $this->postJson(route('billing.webhook'), $payload);

        $response->assertStatus(400)
            ->assertJson(['status' => 'error', 'message' => 'Invalid webhook notification.']);

        $this->assertSame('pending', $this->invoice->fresh()->payment_status);
        $this->assertSame('past_due', $this->tenant->fresh()->status);
    }

    public function test_valid_webhook_request_is_processed(): void
    {
        $payload = $this->payload([
            'transaction_status' => 'settlement',
            'transaction_id' => 'txn-valid-route',
        ]);

        $response = $this->postJson(route('billing.webhook'), $payload);

        $response->assertOk()
            ->assertJson(['status' => 'ok']);

        $this->assertSame('paid', $this->invoice->fresh()->payment_status);
        $this->assertSame('active', $this->tenant->fresh()->status);
    }

    public function test_amount_mismatch_is_rejected_and_does_not_activate_tenant(): void
    {
        app(BillingService::class)->handleWebhookNotification($this->payload([
            'gross_amount' => '100000.00',
            'transaction_status' => 'settlement',
            'transaction_id' => 'txn-amount-mismatch',
        ]));

        $this->assertSame('pending', $this->invoice->fresh()->payment_status);
        $this->assertSame('past_due', $this->tenant->fresh()->status);
        $this->assertSame('amount_mismatch', $this->invoice->fresh()->metadata['midtrans_validation_failure']);
        $this->assertCount(1, $this->invoice->fresh()->metadata['midtrans_webhooks']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = [], bool $sign = true): array
    {
        $payload = array_merge([
            'order_id' => $this->invoice->invoice_number,
            'status_code' => '200',
            'gross_amount' => '125000.00',
            'currency' => 'IDR',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'txn-default',
        ], $overrides);

        if ($sign) {
            $payload['signature_key'] = hash(
                'sha512',
                $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key'),
            );
        }

        return $payload;
    }
}
