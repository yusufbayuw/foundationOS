<?php

namespace Modules\Boarding\Exceptions;

use RuntimeException;

class DuplicateDormitoryCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Dormitory code [{$code}] already exists for this tenant scope.");
    }
}
