<?php

namespace Modules\Messaging\DTO;

class PushNotificationResult
{
    public function __construct(
        public bool $successful,
        public bool $invalidToken = false,
        public ?string $providerMessageId = null,
        public ?string $errorMessage = null,
    ) {}
}
