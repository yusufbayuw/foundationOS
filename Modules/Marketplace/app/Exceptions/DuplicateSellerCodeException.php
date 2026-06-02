<?php

namespace Modules\Marketplace\Exceptions;

use RuntimeException;

class DuplicateSellerCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Seller code [{$code}] already exists for this tenant scope.");
    }
}
