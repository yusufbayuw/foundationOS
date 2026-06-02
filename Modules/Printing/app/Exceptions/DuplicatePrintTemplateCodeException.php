<?php

namespace Modules\Printing\Exceptions;

use RuntimeException;

class DuplicatePrintTemplateCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Print template code [{$code}] already exists for this tenant scope.");
    }
}
