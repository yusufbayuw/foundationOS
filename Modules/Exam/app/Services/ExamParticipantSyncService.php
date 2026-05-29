<?php

namespace Modules\Exam\Services;

use Modules\Exam\Enums\ExamAuditAction;
use Modules\Exam\Models\ExamDefinition;

class ExamParticipantSyncService
{
    public function __construct(
        protected ExamParticipantResolver $resolver,
        protected ExamAuditLogger $auditLogger,
    ) {}

    /**
     * @return array{created: int, skipped: int, errors: list<string>, total: int}
     */
    public function sync(ExamDefinition $exam): array
    {
        $result = $this->resolver->syncForExam($exam);
        $result['total'] = $exam->examParticipants()->count();

        $this->auditLogger->log(
            ExamAuditAction::SyncParticipants,
            $exam,
            'Exam participants generated locally.',
            newValues: $result,
        );

        return $result;
    }
}
