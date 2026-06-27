<?php

namespace Modules\Asset\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class Asset extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $table = 'assets';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'asset_category_id',
        'code',
        'name',
        'status',
        'condition',
        'description',
        'qr_code',
        'acquisition_value',
        'useful_life_months',
        'depreciation_method',
        'maintenance_due_at',
        'photos',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'photos' => 'array',
            'acquisition_value' => 'decimal:2',
            'maintenance_due_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<AssetCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }
}
