<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Library\Enums\FineStatus;
use Modules\Library\Enums\LoanStatus;
use Modules\Library\Models\Concerns\ScopesOrganizationVisibility;

class Loan extends Model
{
    use BelongsToTenant, HasFactory, ScopesOrganizationVisibility, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
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
            'status' => LoanStatus::class,
            'fine_status' => FineStatus::class,
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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

    public function isActive(): bool
    {
        $status = $this->status instanceof LoanStatus ? $this->status->value : (string) $this->status;

        return in_array($status, LoanStatus::activeValues(), true) && $this->return_date === null;
    }

    public function isPrintable(): bool
    {
        $status = $this->status instanceof LoanStatus ? $this->status->value : (string) $this->status;

        return in_array($status, LoanStatus::printableValues(), true);
    }

    /**
     * @param  Builder<Loan>  $query
     * @return Builder<Loan>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereIn('status', LoanStatus::activeValues())
            ->whereNull('return_date');
    }
}
