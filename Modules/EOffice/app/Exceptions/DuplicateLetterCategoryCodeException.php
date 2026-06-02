<?php

namespace Modules\EOffice\Exceptions;

use RuntimeException;

class DuplicateLetterCategoryCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Letter category code [{$code}] already exists for this tenant scope.");
    }
}
