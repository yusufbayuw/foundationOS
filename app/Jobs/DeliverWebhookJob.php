<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Modules\Monitoring\Models\WebhookDelivery;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $timeout = 30;

    public function __construct(private readonly int $deliveryId) {}

    public function handle(): void
    {
        $delivery = WebhookDelivery::with('subscription')->find($this->deliveryId);

        if (! $delivery || $delivery->isDelivered()) {
            return;
        }

        $subscription = $delivery->subscription;

        if (! $subscription || ! $subscription->is_active) {
            $delivery->update(['status' => 'failed', 'error_message' => 'Subscription inactive or deleted.']);

            return;
        }

        $body = json_encode($delivery->payload, JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            $delivery->update(['status' => 'failed', 'error_message' => 'Unable to encode webhook payload.']);

            return;
        }

        $signature = 'sha256='.hash_hmac('sha256', $body, $subscription->secret);

        $delivery->increment('attempt_count');

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Hub-Signature-256' => $signature,
                    'X-Webhook-Event' => $delivery->event,
                    'X-Delivery-Id' => (string) $delivery->id,
                ])
                ->post($subscription->url, $delivery->payload);

            $delivery->update([
                'status' => $response->successful() ? 'delivered' : 'failed',
                'response_status' => $response->status(),
                'response_body' => substr($response->body(), 0, 2000),
                'next_retry_at' => null,
            ]);

            if (! $response->successful()) {
                $this->maybeRelease($delivery);
            }
        } catch (\Throwable $e) {
            $delivery->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->maybeRelease($delivery);
        }
    }

    /**
     * @return list<int>
     */
    public function backoff(): array
    {
        // Exponential backoff: 1m, 5m, 30m, 2h, 8h
        return [60, 300, 1800, 7200, 28800];
    }

    private function maybeRelease(WebhookDelivery $delivery): void
    {
        if ($this->attempts() < $this->tries) {
            $backoff = $this->backoff();
            $seconds = $backoff[$this->attempts() - 1] ?? 28800;
            $delivery->update(['next_retry_at' => now()->addSeconds($seconds)]);
            $this->release($seconds);
        }
    }
}
