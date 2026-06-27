<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @mixin TenantDelegatedRelations
 */
class Tenant extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'domain', 'status', 'subscription_plan_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'domain',
        'subdomain',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'timezone',
        'currency',
        'locale',
        'billing_cycle',
        'status',
        'trial_ends_at',
        'subscribed_at',
        'subscription_expires_at',
        'settings',
        'max_users',
        'max_organizations',
        'max_storage_mb',
        'meta_title',
        'meta_description',
        'subscription_plan_id',
        'grace_period_ends_at',
        'midtrans_customer_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'subscribed_at' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'grace_period_ends_at' => 'datetime',
            'settings' => 'array',
            'max_users' => 'integer',
            'max_organizations' => 'integer',
            'max_storage_mb' => 'integer',
        ];
    }

    public function isSubscriptionActive(): bool
    {
        return in_array($this->status, ['active', 'trial'], true)
            && ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    public function isInGracePeriod(): bool
    {
        return $this->status === 'past_due'
            && $this->grace_period_ends_at !== null
            && $this->grace_period_ends_at->isFuture();
    }

    public function isLocked(): bool
    {
        return $this->status === 'suspended'
            || ($this->status === 'past_due' && ($this->grace_period_ends_at === null || $this->grace_period_ends_at->isPast()));
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function tenantRoles(): HasMany
    {
        return $this->hasMany(TenantRole::class);
    }

    public function userTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }

    public function subscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class);
    }

    public function tenantSettings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'tenant_modules')
            ->withPivot(['is_enabled', 'enabled_at', 'disabled_at', 'settings'])
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tenant_roles')
            ->withPivot([
                'organization_id',
                'tenant_role_id',
                'assigned_by',
                'assigned_at',
                'expires_at',
                'is_primary',
            ])
            ->withTimestamps();
    }
}
