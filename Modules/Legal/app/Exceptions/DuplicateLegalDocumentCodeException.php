<?php

namespace Modules\Legal\Exceptions;

use RuntimeException;

class DuplicateLegalDocumentCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Legal document code [{$code}] already exists for this tenant scope.");
    }
}
