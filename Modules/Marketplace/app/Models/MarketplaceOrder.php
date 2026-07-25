<?php

namespace Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class MarketplaceOrder extends Model
{
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'status', 'name'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    use BelongsToTenant, LogsActivity;

    protected $table = 'marketplace_orders';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'status',
        'description',
        'meta',
        'seller_id',
    ];

    public function getCustomerNameAttribute(): ?string
    {
        return $this->metaValue('customer_name') ?? $this->metaValue('user_name') ?? $this->name;
    }

    public function getTotalAttribute(): mixed
    {
        return $this->metaValue('total') ?? $this->metaValue('total_amount');
    }

    public function getPickupStatusAttribute(): ?string
    {
        return $this->metaValue('pickup_status');
    }

    public function getPickupDateAttribute(): mixed
    {
        return $this->metaValue('pickup_date') ?? $this->metaValue('picked_up_at');
    }

    protected function metaValue(string $key): mixed
    {
        return is_array($this->meta) ? ($this->meta[$key] ?? null) : null;
    }

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
