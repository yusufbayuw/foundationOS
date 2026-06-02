<?php

namespace Modules\Risk\Exceptions;

use RuntimeException;

class DuplicateRiskCategoryCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Risk category code [{$code}] already exists for this tenant scope.");
    }
}
