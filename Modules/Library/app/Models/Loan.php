<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'book_copy_id',
        'member_id',
        'processed_by',
        'returned_by',
        'loan_date',
        'due_date',
        'return_date',
        'extension_count',
        'max_extensions',
        'status',
        'fine_amount',
        'fine_paid',
        'fine_status',
        'condition_on_loan',
        'condition_on_return',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
            'extension_count' => 'integer',
            'max_extensions' => 'integer',
            'fine_amount' => 'decimal:2',
            'fine_paid' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }
}
