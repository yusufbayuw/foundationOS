<?php

namespace Modules\Counseling\Exceptions;

use RuntimeException;

class DuplicateCounselorCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Counselor code [{$code}] already exists for this tenant scope.");
    }
}
