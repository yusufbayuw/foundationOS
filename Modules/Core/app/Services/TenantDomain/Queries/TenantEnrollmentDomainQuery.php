<?php

namespace Modules\Core\Services\TenantDomain\Queries;

use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Models\ExamResult;
use Modules\Enrollment\Models\ExamSchedule;
use Modules\Enrollment\Models\Registration;

final class TenantEnrollmentDomainQuery implements TenantDomainQueryInterface
{
    public function relations(): array
    {
        return [
            'admissionPeriods' => new TenantDomainRelationDefinition('admissionPeriods', AdmissionPeriod::class),
            'applicants' => new TenantDomainRelationDefinition('applicants', Applicant::class),
            'examSchedules' => new TenantDomainRelationDefinition('examSchedules', ExamSchedule::class),
            'examResults' => new TenantDomainRelationDefinition('examResults', ExamResult::class),
            'registrations' => new TenantDomainRelationDefinition('registrations', Registration::class),
        ];
    }
}
