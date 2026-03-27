<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'price_monthly',
        'price_yearly',
        'max_users',
        'max_organizations',
        'max_storage_gb',
        'included_modules',
        'features',
        'is_active',
        'is_recommended',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'max_users' => 'integer',
            'max_organizations' => 'integer',
            'max_storage_gb' => 'integer',
            'included_modules' => 'array',
            'features' => 'array',
            'is_active' => 'boolean',
            'is_recommended' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function previousSubscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class, 'previous_plan_id');
    }

    public function newSubscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class, 'new_plan_id');
    }
}
