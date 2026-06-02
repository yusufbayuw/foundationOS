<?php

namespace Modules\Clinic\Exceptions;

use RuntimeException;

class DuplicateAllergyCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Allergy code [{$code}] already exists for this tenant scope.");
    }
}
