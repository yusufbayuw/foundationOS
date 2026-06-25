<?php

namespace Modules\Messaging\Jobs;

use App\Concerns\InteractsWithTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Models\User;
use Modules\Messaging\Models\NotificationDelivery;
use Modules\Messaging\Services\NotificationDispatcher;

class DeliverNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use InteractsWithTenant;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public int $deliveryId)
    {
        $this->captureCurrentTenant();
    }

    public function handle(NotificationDispatcher $dispatcher): void
    {
        $delivery = NotificationDelivery::query()->find($this->deliveryId);

        if ($delivery === null || $delivery->status === 'sent') {
            return;
        }

        if ($this->tenantId === null && $delivery->tenant_id !== null) {
            $this->tenantId = $delivery->tenant_id;
        }

        $userId = data_get($delivery->meta, 'user_id');
        $user = $userId ? User::query()->find($userId) : null;

        if (! $user instanceof User) {
            $delivery->update([
                'status' => 'failed',
                'description' => 'Recipient user not found for queued notification delivery.',
            ]);

            return;
        }

        /** @var list<string> $channels */
        $channels = data_get($delivery->meta, 'deferred_channels')
            ?? data_get($delivery->meta, 'channels', []);

        $dispatcher->deliverChannels(
            $user,
            (string) $delivery->name,
            (string) ($delivery->description ?? ''),
            $channels,
        );

        $delivery->update(['status' => 'sent']);
    }
}
