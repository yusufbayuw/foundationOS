<?php

namespace Modules\Enrollment\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\Applicant;
use Modules\School\Models\Student;

/**
 * Promotes an accepted applicant to a school student record.
 */
class ApplicantPromotionService
{
    public const SETTING_GROUP = 'enrollment';

    public const SETTING_KEY = 'auto_promote_accepted_applicant';

    public function isEnabledFor(int $tenantId): bool
    {
        return $this->booleanSetting($tenantId, self::SETTING_KEY, true);
    }

    public function promote(Applicant $applicant, ?User $actor = null): ?Student
    {
        if (! $this->isEnabledFor((int) $applicant->tenant_id)) {
            return null;
        }

        $applicant = $applicant->fresh(['admissionPeriod']);

        if ($applicant->converted_to_student_id) {
            return $applicant->convertedStudent;
        }

        return DB::transaction(function () use ($applicant): Student {
            $period = $applicant->admissionPeriod;
            $organizationId = $period?->organization_id;

            $academicYearId = AcademicYear::query()
                ->where('tenant_id', $applicant->tenant_id)
                ->when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
                ->where('is_active', true)
                ->orderByDesc('start_date')
                ->value('id');

            $student = Student::query()->create([
                'tenant_id' => $applicant->tenant_id,
                'organization_id' => $organizationId,
                'academic_year_id' => $academicYearId,
                'nis' => $this->generateNis($applicant),
                'nisn' => $applicant->nisn,
                'entry_date' => now()->toDateString(),
                'entry_type' => 'new_admission',
                'previous_school' => $applicant->previous_school,
                'status' => 'active',
                'ijazah_number' => $applicant->ijazah_number,
                'father_name' => $applicant->parent_name,
                'father_phone' => $applicant->parent_phone,
            ]);

            $applicant->forceFill([
                'converted_to_student_id' => $student->getKey(),
                'enrollment_date' => now()->toDateString(),
            ])->saveQuietly();

            return $student;
        });
    }

    protected function generateNis(Applicant $applicant): string
    {
        $base = 'ADM-'.($applicant->registration_number ?: $applicant->getKey());
        $candidate = $base;
        $seq = 1;

        while (Student::withoutTenantScope()
            ->where('tenant_id', $applicant->tenant_id)
            ->where('nis', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $base.'-'.$seq;
        }

        return $candidate;
    }

    protected function booleanSetting(int $tenantId, string $key, bool $default): bool
    {
        $setting = TenantSetting::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('group', self::SETTING_GROUP)
            ->where('key', $key)
            ->first();

        if (! $setting) {
            return $default;
        }

        return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
    }
}
