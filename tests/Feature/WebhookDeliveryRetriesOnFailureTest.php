<?php

namespace Tests\Feature;

use App\Jobs\DeliverWebhookJob;
use App\Services\WebhookDispatcher;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Monitoring\Models\WebhookDelivery;
use Modules\Monitoring\Models\WebhookSubscription;
use Tests\TestCase;

class WebhookDeliveryRetriesOnFailureTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'webhook-plan',
            'name' => 'Webhook Plan',
            'included_modules' => ['core'],
        ]);

        $user = User::create([
            'name' => 'Webhook Admin',
            'email' => 'webhook@example.com',
            'password' => 'password',
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-webhook',
            'name' => 'Webhook Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    public function test_dispatcher_creates_delivery_record_for_matching_subscription(): void
    {
        Queue::fake();

        $subscription = WebhookSubscription::create([
            'tenant_id' => $this->tenant->id,
            'url' => 'https://example.com/webhook',
            'events' => ['enrollment.created'],
            'secret' => 'test-secret',
            'is_active' => true,
        ]);

        $dispatcher = app(WebhookDispatcher::class);
        $dispatcher->dispatch($this->tenant->id, 'enrollment.created', ['id' => 1]);

        $this->assertDatabaseHas('webhook_deliveries', [
            'webhook_subscription_id' => $subscription->id,
            'event' => 'enrollment.created',
            'status' => 'pending',
        ]);

        Queue::assertPushed(DeliverWebhookJob::class);
    }

    public function test_dispatcher_skips_inactive_subscriptions(): void
    {
        Queue::fake();

        WebhookSubscription::create([
            'tenant_id' => $this->tenant->id,
            'url' => 'https://example.com/webhook',
            'events' => ['enrollment.created'],
            'secret' => 'test-secret',
            'is_active' => false,
        ]);

        $dispatcher = app(WebhookDispatcher::class);
        $dispatcher->dispatch($this->tenant->id, 'enrollment.created', ['id' => 1]);

        $this->assertDatabaseMissing('webhook_deliveries', ['event' => 'enrollment.created']);
        Queue::assertNotPushed(DeliverWebhookJob::class);
    }

    public function test_dispatcher_skips_non_matching_events(): void
    {
        Queue::fake();

        WebhookSubscription::create([
            'tenant_id' => $this->tenant->id,
            'url' => 'https://example.com/webhook',
            'events' => ['payment.verified'],
            'secret' => 'test-secret',
            'is_active' => true,
        ]);

        $dispatcher = app(WebhookDispatcher::class);
        $dispatcher->dispatch($this->tenant->id, 'enrollment.created', ['id' => 1]);

        $this->assertDatabaseMissing('webhook_deliveries', ['event' => 'enrollment.created']);
    }

    public function test_wildcard_subscription_receives_any_event(): void
    {
        Queue::fake();

        $subscription = WebhookSubscription::create([
            'tenant_id' => $this->tenant->id,
            'url' => 'https://example.com/webhook',
            'events' => ['*'],
            'secret' => 'test-secret',
            'is_active' => true,
        ]);

        $dispatcher = app(WebhookDispatcher::class);
        $dispatcher->dispatch($this->tenant->id, 'any.event', ['id' => 99]);

        $this->assertDatabaseHas('webhook_deliveries', [
            'webhook_subscription_id' => $subscription->id,
            'event' => 'any.event',
        ]);
    }

    public function test_delivery_job_marks_status_as_delivered_on_success(): void
    {
        Http::fake(['https://example.com/webhook' => Http::response('OK', 200)]);

        $subscription = WebhookSubscription::create([
            'tenant_id' => $this->tenant->id,
            'url' => 'https://example.com/webhook',
            'events' => ['enrollment.created'],
            'secret' => 'my-secret',
            'is_active' => true,
        ]);

        $delivery = WebhookDelivery::create([
            'webhook_subscription_id' => $subscription->id,
            'event' => 'enrollment.created',
            'payload' => ['id' => 1],
            'status' => 'pending',
        ]);

        DeliverWebhookJob::dispatchSync($delivery->id);

        $this->assertDatabaseHas('webhook_deliveries', [
            'id' => $delivery->id,
            'status' => 'delivered',
            'response_status' => 200,
        ]);
    }

    public function test_delivery_job_marks_status_as_failed_on_http_error(): void
    {
        Http::fake(['https://example.com/webhook' => Http::response('Error', 500)]);

        $subscription = WebhookSubscription::create([
            'tenant_id' => $this->tenant->id,
            'url' => 'https://example.com/webhook',
            'events' => ['payment.verified'],
            'secret' => 'my-secret',
            'is_active' => true,
        ]);

        $delivery = WebhookDelivery::create([
            'webhook_subscription_id' => $subscription->id,
            'event' => 'payment.verified',
            'payload' => ['id' => 1],
            'status' => 'pending',
        ]);

        // Dispatch synchronously — will fail but not retry in sync mode.
        DeliverWebhookJob::dispatchSync($delivery->id);

        $delivery->refresh();
        $this->assertSame(1, $delivery->attempt_count);
        $this->assertSame(500, $delivery->response_status);
    }

    public function test_delivery_job_increments_attempt_count_on_failure(): void
    {
        Http::fake(['https://example.com/webhook' => Http::response('Bad Gateway', 502)]);

        $subscription = WebhookSubscription::create([
            'tenant_id' => $this->tenant->id,
            'url' => 'https://example.com/webhook',
            'events' => ['workflow.completed'],
            'secret' => 'secret-key',
            'is_active' => true,
        ]);

        $delivery = WebhookDelivery::create([
            'webhook_subscription_id' => $subscription->id,
            'event' => 'workflow.completed',
            'payload' => ['instance_id' => 42],
            'status' => 'pending',
            'attempt_count' => 0,
        ]);

        DeliverWebhookJob::dispatchSync($delivery->id);

        $this->assertDatabaseHas('webhook_deliveries', [
            'id' => $delivery->id,
            'attempt_count' => 1,
        ]);
    }

    public function test_delivery_job_sends_hmac_signature_header(): void
    {
        $capturedHeaders = [];

        Http::fake(function ($request) use (&$capturedHeaders) {
            $capturedHeaders = $request->headers();

            return Http::response('OK', 200);
        });

        $subscription = WebhookSubscription::create([
            'tenant_id' => $this->tenant->id,
            'url' => 'https://example.com/webhook',
            'events' => ['enrollment.created'],
            'secret' => 'my-hmac-secret',
            'is_active' => true,
        ]);

        $delivery = WebhookDelivery::create([
            'webhook_subscription_id' => $subscription->id,
            'event' => 'enrollment.created',
            'payload' => ['id' => 5],
            'status' => 'pending',
        ]);

        DeliverWebhookJob::dispatchSync($delivery->id);

        $this->assertArrayHasKey('x-hub-signature-256', array_change_key_case($capturedHeaders, CASE_LOWER));
        $this->assertStringStartsWith('sha256=', array_change_key_case($capturedHeaders, CASE_LOWER)['x-hub-signature-256'][0] ?? '');
    }

    public function test_delivery_job_skips_inactive_subscription(): void
    {
        Http::fake();

        $subscription = WebhookSubscription::create([
            'tenant_id' => $this->tenant->id,
            'url' => 'https://example.com/webhook',
            'events' => ['enrollment.created'],
            'secret' => 'secret',
            'is_active' => false,
        ]);

        $delivery = WebhookDelivery::create([
            'webhook_subscription_id' => $subscription->id,
            'event' => 'enrollment.created',
            'payload' => ['id' => 1],
            'status' => 'pending',
        ]);

        DeliverWebhookJob::dispatchSync($delivery->id);

        $this->assertDatabaseHas('webhook_deliveries', [
            'id' => $delivery->id,
            'status' => 'failed',
        ]);

        Http::assertNothingSent();
    }
}
