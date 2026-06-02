<?php

namespace Modules\Event\Exceptions;

use RuntimeException;

class DuplicateEventCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Event code [{$code}] already exists for this tenant scope.");
    }
}
