<?php

namespace Modules\Core\Services\TenantDomain\Queries;

use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;
use Modules\School\Models\AchievementType;
use Modules\School\Models\Assessment;
use Modules\School\Models\AssessmentItem;
use Modules\School\Models\Attendance;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Curriculum;
use Modules\School\Models\Schedule;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\StudentAchievement;
use Modules\School\Models\StudentAssessmentAnswer;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Subject;
use Modules\School\Models\Teacher;
use Modules\School\Models\Violation;
use Modules\School\Models\ViolationType;

final class TenantSchoolDomainQuery implements TenantDomainQueryInterface
{
    public function relations(): array
    {
        return [
            'curricula' => new TenantDomainRelationDefinition('curricula', Curriculum::class),
            'subjects' => new TenantDomainRelationDefinition('subjects', Subject::class),
            'students' => new TenantDomainRelationDefinition('students', Student::class),
            'teachers' => new TenantDomainRelationDefinition('teachers', Teacher::class),
            'schoolClasses' => new TenantDomainRelationDefinition('schoolClasses', SchoolClass::class),
            'classStudents' => new TenantDomainRelationDefinition('classStudents', ClassStudent::class),
            'schedules' => new TenantDomainRelationDefinition('schedules', Schedule::class),
            'attendances' => new TenantDomainRelationDefinition('attendances', Attendance::class),
            'assessments' => new TenantDomainRelationDefinition('assessments', Assessment::class),
            'assessmentItems' => new TenantDomainRelationDefinition('assessmentItems', AssessmentItem::class),
            'studentAssessmentAnswers' => new TenantDomainRelationDefinition('studentAssessmentAnswers', StudentAssessmentAnswer::class),
            'studentGrades' => new TenantDomainRelationDefinition('studentGrades', StudentGrade::class),
            'violationTypes' => new TenantDomainRelationDefinition('violationTypes', ViolationType::class),
            'violations' => new TenantDomainRelationDefinition('violations', Violation::class),
            'achievementTypes' => new TenantDomainRelationDefinition('achievementTypes', AchievementType::class),
            'studentAchievements' => new TenantDomainRelationDefinition('studentAchievements', StudentAchievement::class),
        ];
    }
}
