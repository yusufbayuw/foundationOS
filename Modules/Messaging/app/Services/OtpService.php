<?php

namespace Modules\Messaging\Services;

use Modules\Core\Models\User;
use Modules\Messaging\Models\NotificationDelivery;

class OtpService
{
    public const TemplateOtp = 'OTP';

    public const TemplateAccountVerification = 'ACCOUNT_VERIFICATION';

    public const TemplateForgotPassword = 'FORGOT_PASSWORD';

    public const TemplateDonationReport = 'DONATION_REPORT';

    public function __construct(
        protected NotificationDispatcher $dispatcher,
    ) {}

    /**
     * @param  list<string>  $channels
     * @param  array<string, mixed>  $variables
     */
    public function sendOtp(
        User $user,
        string $code,
        array $channels = ['whatsapp', 'sms'],
        string $templateCode = self::TemplateOtp,
        array $variables = [],
        ?string $idempotencyKey = null,
    ): NotificationDelivery {
        return $this->dispatcher->dispatchTemplate(
            $user,
            $templateCode,
            array_merge($variables, ['otp' => $code, 'code' => $code]),
            $channels,
            $idempotencyKey ?? "otp:{$user->getKey()}:{$templateCode}:{$code}",
        );
    }
}
