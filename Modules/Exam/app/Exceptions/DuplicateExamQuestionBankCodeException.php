<?php

namespace Modules\Exam\Exceptions;

use RuntimeException;

class DuplicateExamQuestionBankCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Exam question bank code [{$code}] already exists for this tenant scope.");
    }
}
