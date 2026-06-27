<?php

namespace Modules\Core\Services\TenantDomain\Queries;

use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;
use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmploymentContract;
use Modules\Employee\Models\KpiIndicator;
use Modules\Employee\Models\KpiScore;
use Modules\Employee\Models\KpiTemplate;
use Modules\Employee\Models\LeaveRequest;
use Modules\Employee\Models\PayrollComponent;
use Modules\Employee\Models\Position;
use Modules\Employee\Models\SalarySlip;
use Modules\Employee\Models\SalarySlipComponent;
use Modules\Employee\Models\Shift;

final class TenantEmployeeDomainQuery implements TenantDomainQueryInterface
{
    public function relations(): array
    {
        return [
            'positions' => new TenantDomainRelationDefinition('positions', Position::class),
            'shifts' => new TenantDomainRelationDefinition('shifts', Shift::class),
            'employees' => new TenantDomainRelationDefinition('employees', Employee::class),
            'employmentContracts' => new TenantDomainRelationDefinition('employmentContracts', EmploymentContract::class),
            'attendanceLogs' => new TenantDomainRelationDefinition('attendanceLogs', AttendanceLog::class),
            'leaveRequests' => new TenantDomainRelationDefinition('leaveRequests', LeaveRequest::class),
            'payrollComponents' => new TenantDomainRelationDefinition('payrollComponents', PayrollComponent::class),
            'salarySlips' => new TenantDomainRelationDefinition('salarySlips', SalarySlip::class),
            'salarySlipComponents' => new TenantDomainRelationDefinition('salarySlipComponents', SalarySlipComponent::class),
            'kpiTemplates' => new TenantDomainRelationDefinition('kpiTemplates', KpiTemplate::class),
            'kpiIndicators' => new TenantDomainRelationDefinition('kpiIndicators', KpiIndicator::class),
            'kpiScores' => new TenantDomainRelationDefinition('kpiScores', KpiScore::class),
        ];
    }
}
