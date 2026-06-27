<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Tenant;

class RfqVendor extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'request_for_quotation_id',
        'vendor_id',
        'invitation_date',
        'response_deadline',
        'status',
        'responded_at',
        'quotation_amount',
        'quotation_document',
        'technical_score',
        'price_score',
        'total_score',
        'ranking',
        'is_shortlisted',
        'is_awarded',
        'award_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'invitation_date' => 'date',
            'response_deadline' => 'date',
            'responded_at' => 'datetime',
            'quotation_amount' => 'decimal:2',
            'technical_score' => 'decimal:2',
            'price_score' => 'decimal:2',
            'total_score' => 'decimal:2',
            'ranking' => 'integer',
            'is_shortlisted' => 'boolean',
            'is_awarded' => 'boolean',
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
     * @return BelongsTo<Vendor, $this>
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
