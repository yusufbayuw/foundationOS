<?php

namespace Modules\Transport\Exceptions;

use RuntimeException;

class DuplicateRouteCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Route code [{$code}] already exists for this tenant scope.");
    }
}
