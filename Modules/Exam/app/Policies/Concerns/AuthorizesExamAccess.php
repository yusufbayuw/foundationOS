<?php

namespace Modules\Exam\Policies\Concerns;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamAuthorizationService;

trait AuthorizesExamAccess
{
    protected function examAuth(): ExamAuthorizationService
    {
        return app(ExamAuthorizationService::class);
    }

    protected function canAccessExam(AuthUser $user, ExamDefinition $exam, string $permission): bool
    {
        return $this->examAuth()->authorize($user, $exam, $permission);
    }

    protected function canViewExam(AuthUser $user, ExamDefinition $exam): bool
    {
        return $this->examAuth()->authorize($user, $exam, 'view_exam');
    }
}
