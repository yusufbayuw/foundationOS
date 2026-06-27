<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Tenant;

class RfqItem extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'request_for_quotation_id',
        'procurement_item_id',
        'description',
        'specifications',
        'quantity',
        'unit_of_measure',
        'estimated_budget',
        'technical_requirements',
        'mandatory_requirements',
        'scoring_criteria',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'estimated_budget' => 'decimal:2',
            'mandatory_requirements' => 'array',
            'scoring_criteria' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<RequestForQuotation, $this>
     */
    public function requestForQuotation(): BelongsTo
    {
        return $this->belongsTo(RequestForQuotation::class);
    }

    /**
     * @return BelongsTo<ProcurementItem, $this>
     */
    public function procurementItem(): BelongsTo
    {
        return $this->belongsTo(ProcurementItem::class);
    }
}
