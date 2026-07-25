<?php

namespace Modules\Messaging\Services;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Messaging\Contracts\MessageGateway;
use Modules\Messaging\Contracts\WhatsAppProvider;
use Modules\Messaging\Jobs\SendPushNotificationJob;
use Modules\Messaging\Models\NotificationDelivery;
use Modules\Messaging\Models\NotificationTemplate;
use Modules\Messaging\Notifications\GenericDatabaseNotification;

class NotificationDispatcher
{
    public function __construct(
        protected WhatsAppProvider $whatsApp,
        protected MessageGateway $messageGateway,
    ) {}

    /**
     * @param  list<string>  $channels
     * @param  array<string, mixed>  $variables
     */
    public function dispatchTemplate(
        User $user,
        string $templateCode,
        array $variables = [],
        array $channels = ['database'],
        ?string $idempotencyKey = null,
    ): NotificationDelivery {
        $tenantId = $this->tenantIdFor($user);
        $template = $this->templateFor($tenantId, $templateCode, $channels[0] ?? 'database');
        $subject = $this->render((string) ($template?->name ?? Str::headline($templateCode)), $variables);
        $body = $this->render((string) ($template?->body_template ?? $template?->description ?? $this->defaultTemplate($templateCode)), $variables);

        return $this->dispatch($user, $templateCode, $subject, $body, $channels, $idempotencyKey, $variables);
    }

    /**
     * @param  list<string>  $channels
     * @param  array<string, mixed>  $variables
     */
    public function dispatch(
        User $user,
        string $category,
        string $subject,
        string $body,
        array $channels = ['database'],
        ?string $idempotencyKey = null,
        array $variables = [],
    ): NotificationDelivery {
        if ($idempotencyKey !== null) {
            $existing = NotificationDelivery::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing !== null) {
                return $existing;
            }
        }

        $tenantId = $this->tenantIdFor($user);
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
                'variables' => $variables,
            ],
            'idempotency_key' => $idempotencyKey,
        ]);

        foreach ($channels as $channel) {
            match ($channel) {
                'database' => $this->sendDatabase($user, $subject, $body),
                'mail' => $this->sendMail($user, $subject, $body),
                'whatsapp' => $this->sendWhatsApp($user, $body, $variables),
                'sms' => $this->sendSms($user, $body, $variables),
                'push' => $this->sendPush($user, $subject, $body, $delivery),
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

    /** @param  array<string, mixed>  $variables */
    protected function sendWhatsApp(User $user, string $body, array $variables = []): void
    {
        if ($user->phone) {
            $this->messageGateway->sendWhatsApp($user->phone, $body, $variables);
        }
    }

    /** @param  array<string, mixed>  $variables */
    protected function sendSms(User $user, string $body, array $variables = []): void
    {
        if ($user->phone) {
            $this->messageGateway->sendSms($user->phone, $body, $variables);
        }
    }

    protected function sendPush(User $user, string $subject, string $body, NotificationDelivery $delivery): void
    {
        SendPushNotificationJob::dispatch(
            userId: $user->getKey(),
            title: $subject,
            body: $body,
            data: [
                'notification_delivery_id' => (string) $delivery->getKey(),
                'category' => (string) $delivery->code,
            ],
            notificationDeliveryId: $delivery->getKey(),
        );
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

    protected function tenantIdFor(User $user): int|string|null
    {
        return $user->tenants()->value('tenants.id')
            ?? $user->userTenantRoles()->value('tenant_id');
    }

    protected function templateFor(int|string|null $tenantId, string $code, string $channel): ?NotificationTemplate
    {
        return NotificationTemplate::withoutTenantScope()
            ->where('code', strtoupper($code))
            ->where('status', 'active')
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereIn('channel', [$channel, 'all'])
            ->latest('version')
            ->first();
    }

    /** @param  array<string, mixed>  $variables */
    protected function render(string $template, array $variables): string
    {
        return preg_replace_callback('/{{\s*([A-Za-z0-9_.-]+)\s*}}/', function (array $matches) use ($variables): string {
            return (string) data_get($variables, $matches[1], '');
        }, $template) ?? $template;
    }

    protected function defaultTemplate(string $code): string
    {
        return match (strtoupper($code)) {
            OtpService::TemplateAccountVerification => 'Kode verifikasi akun Anda adalah {{ otp }}.',
            OtpService::TemplateForgotPassword => 'Kode reset password Anda adalah {{ otp }}.',
            OtpService::TemplateDonationReport => 'Laporan donasi {{ period }} tersedia: {{ total }}.',
            default => 'Kode OTP Anda adalah {{ otp }}.',
        };
    }
}
