<?php

namespace Modules\Facility\Exceptions;

use RuntimeException;

class DuplicateRoomCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Room code [{$code}] already exists for this tenant scope.");
    }
}
