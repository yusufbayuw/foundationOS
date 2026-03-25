<?php

namespace Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'applicant_id',
        'completed_by',
        'registration_date',
        'status',
        'payment_status',
        'total_fee',
        'paid_amount',
        'uniform_size',
        'documents_received',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
            'total_fee' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'documents_received' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
