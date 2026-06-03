<?php

namespace Modules\Ai\Exceptions;

use RuntimeException;

class DuplicateAiPromptTemplateCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("AI prompt template code [{$code}] already exists for this tenant scope.");
    }
}
