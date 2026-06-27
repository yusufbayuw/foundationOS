<?php

namespace Modules\Core\Services\TenantDomain\Queries;

use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\FeederLog;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyProgram;
use Modules\Campus\Models\StudyResult;
use Modules\Campus\Models\Thesis;
use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;

final class TenantCampusDomainQuery implements TenantDomainQueryInterface
{
    public function relations(): array
    {
        return [
            'faculties' => new TenantDomainRelationDefinition('faculties', Faculty::class),
            'studyPrograms' => new TenantDomainRelationDefinition('studyPrograms', StudyProgram::class),
            'courses' => new TenantDomainRelationDefinition('courses', Course::class),
            'lecturers' => new TenantDomainRelationDefinition('lecturers', Lecturer::class),
            'collageStudents' => new TenantDomainRelationDefinition('collageStudents', CollageStudent::class),
            'courseOfferings' => new TenantDomainRelationDefinition('courseOfferings', CourseOffering::class),
            'studyPlans' => new TenantDomainRelationDefinition('studyPlans', StudyPlan::class),
            'studyPlanItems' => new TenantDomainRelationDefinition('studyPlanItems', StudyPlanItem::class),
            'studyResults' => new TenantDomainRelationDefinition('studyResults', StudyResult::class),
            'feederLogs' => new TenantDomainRelationDefinition('feederLogs', FeederLog::class),
            'theses' => new TenantDomainRelationDefinition('theses', Thesis::class),
        ];
    }
}
