<?php

namespace Modules\MerchOrder\Exceptions;

use RuntimeException;

class DuplicateUniformPackageCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Uniform package code [{$code}] already exists for this tenant scope.");
    }
}
