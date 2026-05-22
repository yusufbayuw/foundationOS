<?php

namespace App\Services;

use App\Jobs\DeliverWebhookJob;
use Modules\Monitoring\Models\WebhookDelivery;
use Modules\Monitoring\Models\WebhookSubscription;

class WebhookDispatcher
{
    /**
     * Dispatch a webhook event to all active subscriptions for the given tenant.
     *
     * @param  array<string, mixed>  $payload
     */
    public function dispatch(int|string $tenantId, string $event, array $payload): void
    {
        $subscriptions = WebhookSubscription::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get()
            ->filter(fn (WebhookSubscription $s) => $s->subscribesTo($event));

        foreach ($subscriptions as $subscription) {
            $delivery = WebhookDelivery::create([
                'webhook_subscription_id' => $subscription->id,
                'event' => $event,
                'payload' => array_merge($payload, [
                    'event' => $event,
                    'tenant_id' => $tenantId,
                    'delivered_at' => now()->toIso8601String(),
                ]),
                'status' => 'pending',
            ]);

            DeliverWebhookJob::dispatch($delivery->id);
        }
    }
}
