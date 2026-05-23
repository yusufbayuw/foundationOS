<?php

namespace Modules\Risk\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class Risk extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $table = 'risks';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'status',
        'description',
        'meta',
        'risk_category_id',
        'riskable_type',
        'riskable_id',
        'likelihood',
        'impact',
        'score',
        'residual_likelihood',
        'residual_impact',
        'residual_score',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'likelihood' => 'integer',
            'impact' => 'integer',
            'score' => 'integer',
            'residual_likelihood' => 'integer',
            'residual_impact' => 'integer',
            'residual_score' => 'integer',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
