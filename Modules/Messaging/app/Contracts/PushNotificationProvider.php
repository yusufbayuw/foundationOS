<?php

namespace Modules\Messaging\Contracts;

use Modules\Messaging\DTO\PushNotificationResult;

interface PushNotificationProvider
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function send(string $token, string $title, string $body, array $data = []): PushNotificationResult;
}
