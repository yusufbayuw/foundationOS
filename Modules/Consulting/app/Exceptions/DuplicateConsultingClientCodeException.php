<?php

namespace Modules\Consulting\Exceptions;

use RuntimeException;

class DuplicateConsultingClientCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Consulting client code [{$code}] already exists for this tenant scope.");
    }
}
