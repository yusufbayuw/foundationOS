<?php

namespace Modules\Messaging\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Messaging\Contracts\WhatsAppProvider;
use Modules\Messaging\Jobs\DeliverNotificationJob;
use Modules\Messaging\Models\NotificationDelivery;
use Modules\Messaging\Notifications\GenericDatabaseNotification;

class NotificationDispatcher
{
    /** @var list<string> */
    private const DEFERRED_CHANNELS = ['mail', 'whatsapp', 'sms', 'push', 'telegram'];

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

        $immediateChannels = array_values(array_diff($channels, self::DEFERRED_CHANNELS));
        $deferredChannels = array_values(array_intersect($channels, self::DEFERRED_CHANNELS));

        $delivery = NotificationDelivery::query()->create([
            'tenant_id' => $tenantId,
            'code' => $category,
            'name' => $subject,
            'status' => 'queued',
            'description' => $body,
            'meta' => [
                'channels' => $channels,
                'deferred_channels' => $deferredChannels,
                'user_id' => $user->getKey(),
            ],
            'idempotency_key' => $idempotencyKey,
        ]);

        if ($immediateChannels !== []) {
            $this->deliverChannels($user, $subject, $body, $immediateChannels);
        }

        if ($deferredChannels !== []) {
            DeliverNotificationJob::dispatch($delivery->id)
                ->onTenant($tenantId);

            return $delivery;
        }

        $delivery->update(['status' => 'sent']);

        return $delivery;
    }

    /**
     * @param  list<string>  $channels
     */
    public function deliverChannels(User $user, string $subject, string $body, array $channels): void
    {
        foreach ($channels as $channel) {
            match ($channel) {
                'database' => $this->sendDatabase($user, $subject, $body),
                'mail' => $this->sendMail($user, $subject, $body),
                'whatsapp' => $this->sendWhatsApp($user, $body),
                default => null,
            };
        }
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

        $tenantOverride = Cache::remember(
            "tenant.{$tenantId}.integrations.{$channel}.enabled",
            now()->addMinutes(10),
            fn () => TenantSetting::withoutTenantScope()
                ->where('tenant_id', $tenantId)
                ->where('group', 'integrations')
                ->where('key', "{$channel}.enabled")
                ->value('value'),
        );

        return $tenantOverride === null ? $configured : filter_var($tenantOverride, FILTER_VALIDATE_BOOL);
    }
}
