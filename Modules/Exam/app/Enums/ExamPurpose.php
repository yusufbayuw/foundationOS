<?php

namespace Modules\Exam\Enums;

enum ExamPurpose: string
{
    case Quiz = 'quiz';
    case Test = 'test';
    case Exam = 'exam';
    case TryOut = 'try_out';
    case PlacementTest = 'placement_test';
    case OsnPrep = 'osn_prep';
    case Assessment = 'assessment';
    case MiAssessment = 'mi_assessment';
    case InternalSelection = 'internal_selection';
    case Survey = 'survey';

    public function label(): string
    {
        return match ($this) {
            self::Quiz => 'Quiz',
            self::Test => 'Test',
            self::Exam => 'Exam',
            self::TryOut => 'Try out',
            self::PlacementTest => 'Placement test',
            self::OsnPrep => 'OSN prep',
            self::Assessment => 'Assessment',
            self::MiAssessment => 'MI assessment',
            self::InternalSelection => 'Internal selection',
            self::Survey => 'Survey',
        };
    }
}
