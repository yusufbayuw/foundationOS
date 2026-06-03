<?php

namespace Modules\Capacity\Exceptions;

use RuntimeException;

class DuplicateCapacityResourceCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Capacity resource code [{$code}] already exists for this tenant scope.");
    }
}
