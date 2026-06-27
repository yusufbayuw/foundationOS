<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'price_monthly',
        'price_yearly',
        'price_per_seat',
        'price_per_module',
        'free_seats',
        'free_modules',
        'grace_period_days',
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
            'price_per_seat' => 'decimal:2',
            'price_per_module' => 'decimal:2',
            'free_seats' => 'integer',
            'free_modules' => 'integer',
            'grace_period_days' => 'integer',
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

    public function calculateMonthlyAmount(int $activeSeats, int $activeModules): float
    {
        $billableSeats = max(0, $activeSeats - $this->free_seats);
        $billableModules = max(0, $activeModules - $this->free_modules);

        return (float) $this->price_monthly
            + ($billableSeats * (float) $this->price_per_seat)
            + ($billableModules * (float) $this->price_per_module);
    }

    /**
     * @return HasMany<Tenant, $this>
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * @return HasMany<SubscriptionLog, $this>
     */
    public function previousSubscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class, 'previous_plan_id');
    }

    /**
     * @return HasMany<SubscriptionLog, $this>
     */
    public function newSubscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class, 'new_plan_id');
    }
}
