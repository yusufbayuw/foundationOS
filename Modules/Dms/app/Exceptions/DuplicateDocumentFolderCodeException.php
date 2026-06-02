<?php

namespace Modules\Dms\Exceptions;

use RuntimeException;

class DuplicateDocumentFolderCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Document folder code [{$code}] already exists for this tenant scope.");
    }
}
