<?php

namespace Modules\Messaging\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Models\User;
use Modules\Messaging\Models\NotificationDelivery;
use Modules\Messaging\Services\PushNotificationService;
use Throwable;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public int $userId,
        public string $title,
        public string $body,
        public array $data = [],
        public ?int $notificationDeliveryId = null,
    ) {}

    /**
     * @return list<int>
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    public function handle(PushNotificationService $pushNotifications): void
    {
        $user = User::query()->findOrFail($this->userId);
        $delivery = $this->notificationDeliveryId === null
            ? null
            : NotificationDelivery::query()->find($this->notificationDeliveryId);

        $pushNotifications->sendToUser($user, $this->title, $this->body, $this->data, $delivery);
    }

    public function failed(?Throwable $exception): void
    {
        if ($this->notificationDeliveryId === null) {
            return;
        }

        NotificationDelivery::query()
            ->whereKey($this->notificationDeliveryId)
            ->update([
                'status' => 'failed',
                'meta->push_error' => $exception?->getMessage(),
            ]);
    }
}
