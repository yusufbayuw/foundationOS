<?php

namespace Modules\Exam\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamResultGraded
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public readonly string $examId,
        public readonly string $resultId,
        public readonly string $participantId,
        public readonly array $context = [],
    ) {}
}
