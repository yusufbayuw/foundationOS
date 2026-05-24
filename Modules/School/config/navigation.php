<?php

use Filament\Support\Icons\Heroicon;

return [
    'group' => 'School',
    'icon' => Heroicon::AcademicCap,
    'sort' => 30,
    'resources' => [
        'CurriculumResource' => 10,
        'SubjectResource' => 20,
        'SchoolClassResource' => 30,
        'TeacherResource' => 40,
        'StudentResource' => 50,
        'ClassStudentResource' => 60,
        'ScheduleResource' => 70,
        'AttendanceResource' => 80,
        'AssessmentResource' => 90,
        'AssessmentItemResource' => 100,
        'StudentAssessmentAnswerResource' => 110,
        'StudentGradeResource' => 120,
        'AchievementTypeResource' => 130,
        'StudentAchievementResource' => 140,
        'ViolationTypeResource' => 150,
        'ViolationResource' => 160,
        'ExtracurricularResource' => 170,
    ],
    'pages' => [
        'AttendanceRecapPage' => 190,
        'ReportCardPage' => 195,
        'AcademicAnalytics' => 200,
    ],
];
