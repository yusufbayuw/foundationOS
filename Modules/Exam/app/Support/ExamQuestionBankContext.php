<?php

namespace Modules\Exam\Support;

use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Models\ExamQuestionBank;

class ExamQuestionBankContext
{
    public function validate(ExamQuestionBank $bank): void
    {
        $context = $bank->academic_context_type;

        if ($context === ExamAcademicContext::School) {
            if (
                $bank->school_subject_reference === null
                && blank($bank->school_grade_level_reference)
                && $bank->school_curriculum_reference === null
            ) {
                throw new \InvalidArgumentException(
                    'School question bank requires at least one school reference field.',
                );
            }

            return;
        }

        if ($context === ExamAcademicContext::Campus) {
            if ($bank->campus_course_reference === null && $bank->campus_study_program_reference === null) {
                throw new \InvalidArgumentException(
                    'Campus question bank requires a course or study program reference.',
                );
            }

            return;
        }

        if ($context === ExamAcademicContext::Standalone) {
            if (blank($bank->standalone_subject) && blank($bank->standalone_level)) {
                throw new \InvalidArgumentException(
                    'Standalone question bank requires standalone subject or level.',
                );
            }
        }
    }
}
