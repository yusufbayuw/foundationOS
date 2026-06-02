<?php

namespace Modules\Asset\Exceptions;

use RuntimeException;

class DuplicateAssetCategoryCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Asset category code [{$code}] already exists for this tenant scope.");
    }
}
