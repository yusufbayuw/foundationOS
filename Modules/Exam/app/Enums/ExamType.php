<?php

namespace Modules\Exam\Enums;

enum ExamType: string
{
    case Practice = 'practice';
    case Quiz = 'quiz';
    case Exam = 'exam';
    case Tryout = 'tryout';
    case Placement = 'placement';
    case MiAssessment = 'mi_assessment';
    case OsnPrep = 'osn_prep';

    public function label(): string
    {
        return match ($this) {
            self::Practice => 'Practice',
            self::Quiz => 'Quiz',
            self::Exam => 'Exam',
            self::Tryout => 'Tryout',
            self::Placement => 'Placement',
            self::MiAssessment => 'MI assessment',
            self::OsnPrep => 'OSN prep',
        };
    }

    public function toExamPurpose(): ExamPurpose
    {
        return match ($this) {
            self::Quiz => ExamPurpose::Quiz,
            self::Exam => ExamPurpose::Exam,
            self::Tryout => ExamPurpose::TryOut,
            self::Placement => ExamPurpose::PlacementTest,
            self::MiAssessment => ExamPurpose::MiAssessment,
            self::OsnPrep => ExamPurpose::OsnPrep,
            default => ExamPurpose::Assessment,
        };
    }
}
