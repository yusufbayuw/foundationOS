<?php

namespace Modules\Finance\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\Applicant;
use Modules\Finance\Models\StudentInvoice;

/**
 * Creates a draft registration invoice for an accepted applicant.
 */
class ApplicantOnboardingInvoiceService
{
    public const SETTING_GROUP = 'enrollment';

    public const SETTING_KEY = 'auto_invoice_on_accept';

    public function isEnabledFor(int $tenantId): bool
    {
        return $this->booleanSetting($tenantId, self::SETTING_KEY, true);
    }

    public function createDraftFor(Applicant $applicant, ?User $actor = null): ?StudentInvoice
    {
        if (! $this->isEnabledFor((int) $applicant->tenant_id)) {
            return null;
        }

        $applicant = $applicant->fresh(['admissionPeriod']);

        $amount = (float) ($applicant->admissionPeriod?->registration_fee ?? 0);

        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($applicant, $amount): StudentInvoice {
            $existing = StudentInvoice::query()
                ->where('tenant_id', $applicant->tenant_id)
                ->where('invoiceable_type', $applicant->getMorphClass())
                ->where('invoiceable_id', $applicant->getKey())
                ->whereIn('status', ['draft', 'issued', 'partial'])
                ->first();

            if ($existing) {
                return $existing;
            }

            return StudentInvoice::query()->create([
                'tenant_id' => $applicant->tenant_id,
                'invoiceable_type' => $applicant->getMorphClass(),
                'invoiceable_id' => $applicant->getKey(),
                'invoice_number' => $this->generateInvoiceNumber($applicant),
                'invoice_type' => 'registration',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'amount' => $amount,
                'discount_amount' => 0,
                'penalty_amount' => 0,
                'total_amount' => $amount,
                'paid_amount' => 0,
                'remaining_amount' => $amount,
                'status' => 'draft',
                'description' => sprintf(
                    'Registration fee for %s',
                    (string) $applicant->full_name,
                ),
            ]);
        });
    }

    protected function generateInvoiceNumber(Applicant $applicant): string
    {
        $base = 'INV-ADM-'.($applicant->registration_number ?: $applicant->getKey());
        $candidate = $base;
        $seq = 1;

        while (StudentInvoice::withoutTenantScope()
            ->where('tenant_id', $applicant->tenant_id)
            ->where('invoice_number', $candidate)
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
