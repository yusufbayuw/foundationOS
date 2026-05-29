<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Policies\Concerns\AuthorizesExamAccess;
use Modules\Exam\Services\ExamAuthorizationService;

class ExamResultPolicy
{
    use AuthorizesExamAccess;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return app(ExamAuthorizationService::class)->canViewAny($authUser);
    }

    public function view(AuthUser $authUser, ExamResult $examResult): bool
    {
        $exam = $examResult->examDefinition;

        return $exam !== null && $this->canViewExam($authUser, $exam);
    }

    public function export(AuthUser $authUser, ExamResult $examResult): bool
    {
        $exam = $examResult->examDefinition;

        return $exam !== null
            && $this->canAccessExam($authUser, $exam, 'export_exam_result');
    }
}
