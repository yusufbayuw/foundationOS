<?php

namespace Modules\Messaging\Exceptions;

use RuntimeException;

class DuplicateNotificationTemplateCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Notification template code [{$code}] already exists for this tenant scope.");
    }
}
