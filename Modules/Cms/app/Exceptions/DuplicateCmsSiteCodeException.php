<?php

namespace Modules\Cms\Exceptions;

use RuntimeException;

class DuplicateCmsSiteCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("CMS site code [{$code}] already exists for this tenant.");
    }
}
