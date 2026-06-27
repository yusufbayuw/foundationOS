<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;

class TenantRole extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'level',
        'permissions',
        'is_default',
        'is_super_admin',
        'dashboard_route',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_default' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * @return HasMany<UserTenantRole, $this>
     */
    public function userTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }
}
