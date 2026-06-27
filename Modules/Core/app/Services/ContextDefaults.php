<?php

namespace Modules\Core\Services;

use App\Support\TypedValue;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;

/**
 * Resolves implicit context fields for create forms and models.
 */
class ContextDefaults
{
    /**
     * @return array<string, int|string|null>
     */
    public function forCreate(?Authenticatable $user = null): array
    {
        $user = $user ?? auth()->user();
        $tenantId = TypedValue::tenantKey(Filament::getTenant()?->getKey());
        $createdBy = TypedValue::tenantKey($user?->getAuthIdentifier());

        $defaults = array_filter([
            'tenant_id' => $tenantId,
            'organization_id' => $this->resolveOrganizationId($user, $tenantId),
            'academic_period_id' => $this->resolveAcademicPeriodId($tenantId),
            'academic_year_id' => $this->resolveAcademicYearId($tenantId),
            'created_by' => $createdBy,
        ], fn ($value) => $value !== null);

        /** @var array<string, int|string|null> $defaults */
        return $defaults;
    }

    public function resolveOrganizationId(?Authenticatable $user, int|string|null $tenantId): ?int
    {
        if (! $user instanceof User || ! $tenantId) {
            return null;
        }

        $membership = UserTenantRole::query()
            ->where('user_id', $user->getKey())
            ->where('tenant_id', $tenantId)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->first();

        return $membership?->organization_id ? (int) $membership->organization_id : null;
    }

    public function resolveAcademicPeriodId(int|string|null $tenantId): ?int
    {
        if (! $tenantId) {
            return null;
        }

        $periodId = AcademicPeriod::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->value('id');

        return $periodId ? TypedValue::int($periodId) : null;
    }

    public function resolveAcademicYearId(int|string|null $tenantId): ?int
    {
        if (! $tenantId) {
            return null;
        }

        $yearId = AcademicYear::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->value('id');

        return $yearId ? TypedValue::int($yearId) : null;
    }
}
