<?php

namespace Modules\Library\Support;

use Modules\Core\Models\TenantSetting;
use Modules\Library\Models\LibraryPolicy;
use Modules\Library\Models\Member;

class CirculationPolicyResolver
{
    /**
     * @return array<string, mixed>
     */
    public function resolveForMember(Member $member): array
    {
        $organizationPolicy = null;

        if ($member->organization_id !== null) {
            $organizationPolicy = LibraryPolicy::query()
                ->where('tenant_id', $member->tenant_id)
                ->where('organization_id', $member->organization_id)
                ->where('is_active', true)
                ->first();
        }

        $tenantPolicy = LibraryPolicy::query()
            ->where('tenant_id', $member->tenant_id)
            ->whereNull('organization_id')
            ->where('is_active', true)
            ->first();

        $tenantDefaults = TenantSetting::query()
            ->where('tenant_id', $member->tenant_id)
            ->whereIn('key', [
                'library_default_max_books',
                'library_default_loan_period_days',
                'library_default_fine_per_day',
                'library_default_max_extensions',
                'library_default_grace_period_days',
            ])
            ->pluck('value', 'key');

        $memberType = $member->memberType;

        return [
            'max_books' => $this->positiveInt($organizationPolicy?->max_books, $memberType?->max_books, $tenantPolicy?->max_books, $member->max_books, $tenantDefaults['library_default_max_books'] ?? 3),
            'loan_period_days' => $this->positiveInt($organizationPolicy?->loan_period_days, $memberType?->loan_period_days, $tenantPolicy?->loan_period_days, $member->loan_period_days, $tenantDefaults['library_default_loan_period_days'] ?? 7),
            'fine_per_day' => $this->positiveFloat($organizationPolicy?->fine_per_day, $memberType?->fine_per_day, $tenantPolicy?->fine_per_day, $member->fine_per_day, $tenantDefaults['library_default_fine_per_day'] ?? 1000),
            'max_extensions' => $this->positiveInt($organizationPolicy?->max_extensions, $memberType?->max_extensions, $tenantPolicy?->max_extensions, $tenantDefaults['library_default_max_extensions'] ?? 2),
            'grace_period_days' => $this->nonNegativeInt($organizationPolicy?->grace_period_days, $memberType?->grace_period_days, $tenantPolicy?->grace_period_days, $tenantDefaults['library_default_grace_period_days'] ?? 0),
        ];
    }

    protected function positiveInt(mixed ...$values): int
    {
        foreach ($values as $value) {
            if (is_numeric($value) && (int) $value > 0) {
                return (int) $value;
            }
        }

        return 0;
    }

    protected function nonNegativeInt(mixed ...$values): int
    {
        foreach ($values as $value) {
            if (is_numeric($value) && (int) $value >= 0) {
                return (int) $value;
            }
        }

        return 0;
    }

    protected function positiveFloat(mixed ...$values): float
    {
        foreach ($values as $value) {
            if (is_numeric($value) && (float) $value > 0) {
                return (float) $value;
            }
        }

        return 0.0;
    }
}
