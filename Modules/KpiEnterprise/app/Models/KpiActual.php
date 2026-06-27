<?php

namespace Modules\KpiEnterprise\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class KpiActual extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $table = 'kpi_actuals';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'status',
        'description',
        'meta',
        'kpi_metric_id',
        'period',
        'actual_value',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'actual_value' => 'float',
            'score' => 'float',
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
