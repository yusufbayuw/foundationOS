<?php

namespace Modules\Sales\Exceptions;

use RuntimeException;

class DuplicateCustomerCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Customer code [{$code}] already exists for this tenant scope.");
    }
}
