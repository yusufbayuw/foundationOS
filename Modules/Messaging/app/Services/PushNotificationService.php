<?php

namespace Modules\Messaging\Services;

use App\Models\Device;
use Modules\Core\Models\User;
use Modules\Messaging\Contracts\PushNotificationProvider;
use Modules\Messaging\DTO\PushNotificationResult;
use Modules\Messaging\Models\NotificationDelivery;

class PushNotificationService
{
    public function __construct(
        protected PushNotificationProvider $provider,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function sendToDevice(Device $device, string $title, string $body, array $data = []): PushNotificationResult
    {
        $result = $this->provider->send($device->token, $title, $body, $data);

        if ($result->invalidToken) {
            $device->forceFill(['is_active' => false])->save();
        }

        $device->forceFill(['last_used_at' => now()])->save();

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [], ?NotificationDelivery $delivery = null): int
    {
        $sent = 0;

        $user->devices()
            ->where('is_active', true)
            ->cursor()
            ->each(function (Device $device) use ($title, $body, $data, &$sent): void {
                if ($this->sendToDevice($device, $title, $body, $data)->successful) {
                    $sent++;
                }
            });

        $delivery?->forceFill([
            'status' => $sent > 0 ? 'sent' : 'failed',
            'meta' => array_merge($delivery->meta ?? [], ['push_sent_count' => $sent]),
        ])->save();

        return $sent;
    }
}
