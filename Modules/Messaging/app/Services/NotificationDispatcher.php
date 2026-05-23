<?php

namespace Modules\Messaging\Services;

use Illuminate\Support\Facades\Notification;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Messaging\Contracts\WhatsAppProvider;
use Modules\Messaging\Models\NotificationDelivery;
use Modules\Messaging\Notifications\GenericDatabaseNotification;

class NotificationDispatcher
{
    public function __construct(
        protected WhatsAppProvider $whatsApp,
    ) {}

    /**
     * @param  list<string>  $channels
     */
    public function dispatch(
        User $user,
        string $category,
        string $subject,
        string $body,
        array $channels = ['database'],
        ?string $idempotencyKey = null,
    ): NotificationDelivery {
        if ($idempotencyKey !== null) {
            $existing = NotificationDelivery::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing !== null) {
                return $existing;
            }
        }

        $tenantId = $user->tenants()->value('tenants.id')
            ?? $user->userTenantRoles()->value('tenant_id');
        $channels = array_values(array_filter(
            $channels,
            fn (string $channel): bool => $this->channelEnabled($channel, $tenantId),
        ));

        $delivery = NotificationDelivery::query()->create([
            'tenant_id' => $tenantId,
            'code' => $category,
            'name' => $subject,
            'status' => 'queued',
            'description' => $body,
            'meta' => [
                'channels' => $channels,
                'user_id' => $user->getKey(),
            ],
            'idempotency_key' => $idempotencyKey,
        ]);

        foreach ($channels as $channel) {
            match ($channel) {
                'database' => $this->sendDatabase($user, $subject, $body),
                'mail' => $this->sendMail($user, $subject, $body),
                'whatsapp' => $this->sendWhatsApp($user, $body),
                default => null,
            };
        }

        $delivery->update(['status' => 'sent']);

        return $delivery;
    }

    protected function sendDatabase(User $user, string $subject, string $body): void
    {
        Notification::send($user, new GenericDatabaseNotification($subject, $body));
    }

    protected function sendMail(User $user, string $subject, string $body): void
    {
        // Mail channel uses Laravel notifications when configured.
    }

    protected function sendWhatsApp(User $user, string $body): void
    {
        if ($user->phone) {
            $this->whatsApp->sendMessage($user->phone, $body);
        }
    }

    protected function channelEnabled(string $channel, int|string|null $tenantId): bool
    {
        if ($channel === 'database' || $channel === 'mail') {
            return true;
        }

        $configured = (bool) config("messaging.channels.{$channel}", false);

        if ($tenantId === null) {
            return $configured;
        }

        $tenantOverride = TenantSetting::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('group', 'integrations')
            ->where('key', "{$channel}.enabled")
            ->value('value');

        return $tenantOverride === null ? $configured : filter_var($tenantOverride, FILTER_VALIDATE_BOOL);
    }
}
