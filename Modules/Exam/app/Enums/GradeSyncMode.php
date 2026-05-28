<?php

namespace Modules\Exam\Enums;

enum GradeSyncMode: string
{
    case None = 'none';
    case PushToStudentGrade = 'push_to_student_grade';
    case PushToStudyResult = 'push_to_study_result';
    case ManualReview = 'manual_review';

    public function label(): string
    {
        return match ($this) {
            self::None => 'None',
            self::PushToStudentGrade => 'Push to student grade',
            self::PushToStudyResult => 'Push to study result',
            self::ManualReview => 'Manual review',
        };
    }
}
