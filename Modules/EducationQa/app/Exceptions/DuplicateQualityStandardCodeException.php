<?php

namespace Modules\EducationQa\Exceptions;

use RuntimeException;

class DuplicateQualityStandardCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Quality standard code [{$code}] already exists for this tenant scope.");
    }
}
