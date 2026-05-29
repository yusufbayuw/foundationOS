<?php

namespace Modules\Exam\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamResultPublished
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public readonly string $examId,
        public readonly ?string $resultId = null,
        public readonly ?string $participantId = null,
        public readonly array $context = [],
    ) {}
}
