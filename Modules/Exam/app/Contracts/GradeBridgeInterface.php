<?php

namespace Modules\Exam\Contracts;

use Modules\Exam\Models\ExamAttemptSync;
use Modules\Exam\Models\ExamDefinition;

interface GradeBridgeInterface
{
    public function supports(ExamDefinition $definition): bool;

    public function syncAttempt(ExamDefinition $definition, ExamAttemptSync $attemptSync): void;
}
