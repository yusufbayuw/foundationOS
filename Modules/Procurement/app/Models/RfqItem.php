<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;

class RfqItem extends Model
{
    use HasFactory;

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

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function requestForQuotation(): BelongsTo { return $this->belongsTo(RequestForQuotation::class); }
    public function procurementItem(): BelongsTo { return $this->belongsTo(ProcurementItem::class); }
}
