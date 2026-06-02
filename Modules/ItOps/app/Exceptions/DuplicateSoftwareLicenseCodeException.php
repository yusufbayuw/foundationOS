<?php

namespace Modules\ItOps\Exceptions;

use RuntimeException;

class DuplicateSoftwareLicenseCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Software license code [{$code}] already exists for this tenant scope.");
    }
}
