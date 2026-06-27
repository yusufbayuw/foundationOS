<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'slug',
        'name',
        'description',
        'icon',
        'color',
        'version',
        'is_core',
        'is_active',
        'is_premium',
        'price_monthly',
        'price_yearly',
        'settings_schema',
        'required_modules',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_core' => 'boolean',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'settings_schema' => 'array',
            'required_modules' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<TenantModule, $this>
     */
    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    /**
     * @return BelongsToMany<Tenant, $this>
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_modules')
            ->withPivot(['is_enabled', 'enabled_at', 'disabled_at', 'settings'])
            ->withTimestamps();
    }
}
