<?php

namespace Modules\Cafeteria\Exceptions;

use RuntimeException;

class DuplicateMenuCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Menu code [{$code}] already exists for this tenant scope.");
    }
}
