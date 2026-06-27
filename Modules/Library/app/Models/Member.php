<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;

/**
 * @property int|null $organization_id
 */
class Member extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'user_id',
        'member_number',
        'member_type',
        'member_type_id',
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

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Loan, $this>
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * @return BelongsTo<LibraryMemberType, $this>
     */
    public function memberType(): BelongsTo
    {
        return $this->belongsTo(LibraryMemberType::class, 'member_type_id');
    }

    /**
     * @return HasMany<BookReservation, $this>
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }
}
