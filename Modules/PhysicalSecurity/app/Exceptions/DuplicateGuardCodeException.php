<?php

namespace Modules\PhysicalSecurity\Exceptions;

use RuntimeException;

class DuplicateGuardCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Guard code [{$code}] already exists for this tenant scope.");
    }
}
