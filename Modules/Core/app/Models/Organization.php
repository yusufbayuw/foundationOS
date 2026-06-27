<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Concerns\HasLegacyOrganizationDomainRelations;
use Modules\Global\Models\City;
use Modules\Global\Models\District;
use Modules\Global\Models\Province;
use Modules\Global\Models\Village;

class Organization extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use HasLegacyOrganizationDomainRelations;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'parent_organization_id',
        'organization_type',
        'code',
        'name',
        'short_name',
        'type',
        'level',
        'npsn',
        'nss',
        'accreditation_status',
        'npwp',
        'phone',
        'email',
        'website',
        'address',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'postal_code',
        'latitude',
        'longitude',
        'established_date',
        'principal_user_id',
        'logo',
        'stamp',
        'signature',
        'letterhead',
        'is_main',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
            'established_date' => 'date',
        ];
    }

    public function parentOrganization(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_organization_id');
    }

    public function childOrganizations(): HasMany
    {
        return $this->hasMany(self::class, 'parent_organization_id');
    }

    public function principalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'principal_user_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function organizationSettings(): HasMany
    {
        return $this->hasMany(OrganizationSetting::class);
    }

    public function userTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function academicPeriods(): HasMany
    {
        return $this->hasMany(AcademicPeriod::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tenant_roles')
            ->withPivot([
                'tenant_id',
                'tenant_role_id',
                'assigned_by',
                'assigned_at',
                'expires_at',
                'is_primary',
            ])
            ->withTimestamps();
    }
}
