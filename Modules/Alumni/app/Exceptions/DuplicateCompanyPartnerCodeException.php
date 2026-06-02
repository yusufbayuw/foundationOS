<?php

namespace Modules\Alumni\Exceptions;

use RuntimeException;

class DuplicateCompanyPartnerCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Company partner code [{$code}] already exists for this tenant scope.");
    }
}
