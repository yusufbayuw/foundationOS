<?php

namespace Modules\Library\Services;

use App\Support\TypedValue;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\TenantSetting;
use Modules\Enrollment\Models\Applicant;
use Modules\Library\Models\Member;
use Modules\School\Models\Student;

/**
 * Provisions a library member for a newly accepted applicant / student.
 */
class LibraryMemberProvisioningService
{
    public const SETTING_GROUP = 'library';

    public const SETTING_KEY = 'auto_member_on_accept';

    public function isEnabledFor(int $tenantId): bool
    {
        return $this->booleanSetting($tenantId, self::SETTING_KEY, true);
    }

    public function provisionFor(Applicant $applicant, ?Student $student = null): ?Member
    {
        if (! $this->isEnabledFor((int) $applicant->tenant_id)) {
            return null;
        }

        $applicant = TypedValue::model($applicant->fresh(['admissionPeriod', 'convertedStudent']));
        $student ??= $applicant->convertedStudent;

        if (! $student?->user_id) {
            return null;
        }

        $memberNumber = (string) ($applicant->registration_number ?: 'ADM-'.TypedValue::string($applicant->getKey()));

        return DB::transaction(function () use ($applicant, $student, $memberNumber): Member {
            $existing = Member::query()
                ->where('tenant_id', $applicant->tenant_id)
                ->where('member_number', $memberNumber)
                ->first();

            if ($existing) {
                return $existing;
            }

            return Member::query()->create([
                'tenant_id' => $applicant->tenant_id,
                'organization_id' => $applicant->admissionPeriod?->organization_id,
                'user_id' => $student->user_id,
                'member_number' => $memberNumber,
                'member_type' => 'student',
                'joined_at' => now()->toDateString(),
                'max_books' => 3,
                'loan_period_days' => 14,
                'fine_per_day' => 1000,
                'status' => 'active',
                'notes' => sprintf('Auto-provisioned from applicant #%s', TypedValue::string($applicant->getKey())),
            ]);
        });
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
