<?php

namespace Modules\IsoCompliance\Exceptions;

use RuntimeException;

class DuplicateIsoControlCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("ISO control code [{$code}] already exists for this tenant scope.");
    }
}
