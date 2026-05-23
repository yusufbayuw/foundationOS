<?php

namespace Modules\Asset\Services;

use Modules\Asset\Models\Asset;
use Modules\Asset\Models\AssetDepreciation;
use Modules\Core\Models\Organization;
use Modules\Finance\Models\JournalEntry;

class AssetDepreciationService
{
    /**
     * Generate monthly depreciation entry for an asset and optional draft journal.
     */
    public function recordMonthlyDepreciation(Asset $asset, ?\DateTimeInterface $period = null): AssetDepreciation
    {
        $periodDate = $period ?? now()->startOfMonth();
        $amount = $this->calculateMonthlyAmount($asset);

        $depreciation = AssetDepreciation::query()->updateOrCreate(
            [
                'tenant_id' => $asset->tenant_id,
                'asset_id' => $asset->getKey(),
                'period_date' => $periodDate,
            ],
            [
                'organization_id' => $asset->organization_id,
                'code' => sprintf('DEP-%s-%s', $asset->code ?? $asset->getKey(), $periodDate->format('Ym')),
                'name' => sprintf('Depreciation %s', $asset->name),
                'status' => 'posted',
                'amount' => $amount,
                'meta' => ['method' => $asset->depreciation_method],
            ],
        );

        if ($amount > 0 && $this->resolveOrganizationId($asset) !== null) {
            $this->postDraftJournal($asset, $depreciation, $amount);
        }

        return $depreciation;
    }

    public function calculateMonthlyAmount(Asset $asset): float
    {
        $value = (float) ($asset->acquisition_value ?? 0);
        $months = (int) ($asset->useful_life_months ?? 0);

        if ($value <= 0 || $months <= 0) {
            return 0.0;
        }

        return match ($asset->depreciation_method) {
            'declining' => round($value * (2 / $months), 2),
            default => round($value / $months, 2),
        };
    }

    protected function resolveOrganizationId(Asset $asset): ?int
    {
        return $asset->organization_id
            ?? Organization::withoutTenantScope()
                ->where('tenant_id', $asset->tenant_id)
                ->orderBy('id')
                ->value('id');
    }

    protected function postDraftJournal(Asset $asset, AssetDepreciation $depreciation, float $amount): JournalEntry
    {
        $organizationId = $this->resolveOrganizationId($asset);

        return JournalEntry::query()->create([
            'tenant_id' => $asset->tenant_id,
            'organization_id' => $organizationId,
            'entry_number' => 'JE-DEP-'.$depreciation->getKey(),
            'date' => $depreciation->period_date ?? now(),
            'description' => sprintf('Auto-journal asset depreciation %s', $asset->name),
            'total_debit' => $amount,
            'total_credit' => $amount,
            'is_balanced' => true,
            'is_posted' => false,
        ]);
    }
}
