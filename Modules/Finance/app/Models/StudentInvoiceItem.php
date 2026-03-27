<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;

class StudentInvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'student_invoice_id',
        'tuition_type_id',
        'description',
        'quantity',
        'unit_price',
        'discount_amount',
        'penalty_amount',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function studentInvoice(): BelongsTo
    {
        return $this->belongsTo(StudentInvoice::class);
    }

    public function tuitionType(): BelongsTo
    {
        return $this->belongsTo(TuitionType::class);
    }
}
