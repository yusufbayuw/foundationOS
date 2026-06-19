<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;

class UserTenantRole extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'organization_id',
        'tenant_role_id',
        'assigned_by',
        'assigned_at',
        'expires_at',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_primary' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tenantRole(): BelongsTo
    {
        return $this->belongsTo(TenantRole::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
