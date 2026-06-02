<?php

namespace Modules\Cms\Exceptions;

use RuntimeException;

class DuplicatePageSlugException extends RuntimeException
{
    public function __construct(string $slug)
    {
        parent::__construct("Page slug [{$slug}] already exists for this site.");
    }
}
