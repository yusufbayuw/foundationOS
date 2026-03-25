<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'member_number',
        'member_type',
        'joined_at',
        'expires_at',
        'max_books',
        'loan_period_days',
        'fine_per_day',
        'total_loans_count',
        'current_loans_count',
        'total_fines',
        'unpaid_fines',
        'status',
        'suspension_reason',
        'suspension_until',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'expires_at' => 'date',
            'max_books' => 'integer',
            'loan_period_days' => 'integer',
            'fine_per_day' => 'decimal:2',
            'total_loans_count' => 'integer',
            'current_loans_count' => 'integer',
            'total_fines' => 'decimal:2',
            'unpaid_fines' => 'decimal:2',
            'suspension_until' => 'date',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
