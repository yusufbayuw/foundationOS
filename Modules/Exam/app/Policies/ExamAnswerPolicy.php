<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Models\ExamAnswer;
use Modules\Exam\Policies\Concerns\AuthorizesExamAccess;

class ExamAnswerPolicy
{
    use AuthorizesExamAccess;
    use HandlesAuthorization;

    public function grade(AuthUser $authUser, ExamAnswer $examAnswer): bool
    {
        $exam = $examAnswer->examDefinition;

        return $exam !== null
            && $this->canAccessExam($authUser, $exam, 'grade_exam_answer');
    }
}
