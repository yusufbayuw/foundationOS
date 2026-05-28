<?php

namespace Modules\Exam\Contracts;

use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamPublishSnapshot;

interface RuntimePublisherInterface
{
    /**
     * @return array{runtime_exam_id: string, publish_status: string}
     */
    public function publish(ExamDefinition $definition, ExamPublishSnapshot $snapshot): array;
}
