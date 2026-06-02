<?php

namespace Modules\Property\Exceptions;

use RuntimeException;

class DuplicatePropertyCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Property code [{$code}] already exists for this tenant scope.");
    }
}
