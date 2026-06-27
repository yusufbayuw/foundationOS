<?php

namespace Modules\Facility\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Facility\Database\Factories\RoomFactory;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class Room extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected static function newFactory(): RoomFactory
    {
        return RoomFactory::new();
    }

    protected $table = 'rooms';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'status',
        'description',
        'meta',
        'is_rentable',
        'rental_rate_hourly',
        'rental_rate_daily',
        'is_bookable',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'is_rentable' => 'boolean',
            'is_bookable' => 'boolean',
            'rental_rate_hourly' => 'decimal:2',
            'rental_rate_daily' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
