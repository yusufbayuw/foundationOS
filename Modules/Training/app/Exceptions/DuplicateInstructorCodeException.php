<?php

namespace Modules\Training\Exceptions;

use RuntimeException;

class DuplicateInstructorCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Instructor code [{$code}] already exists for this tenant scope.");
    }
}
