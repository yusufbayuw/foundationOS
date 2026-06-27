<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Procurement\Models\ProcurementItem;
use Modules\Procurement\Models\PurchaseRequisitionItem;

class ChartOfAccount extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'parent_id',
        'code',
        'name',
        'level',
        'type',
        'category',
        'normal_balance',
        'is_bank_account',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'is_active',
        'is_locked',
        'description',
        'opening_balance',
        'current_balance',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'is_bank_account' => 'boolean',
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
            'opening_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
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
     * @return BelongsTo<ChartOfAccount, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<ChartOfAccount, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return HasMany<JournalEntryLine, $this>
     */
    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'chart_of_account_id');
    }

    /**
     * @return HasMany<Budget, $this>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'chart_of_account_id');
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'chart_of_account_id');
    }

    /**
     * @return HasMany<ProcurementItem, $this>
     */
    public function procurementItems(): HasMany
    {
        return $this->hasMany(ProcurementItem::class, 'chart_of_account_id');
    }

    /**
     * @return HasMany<PurchaseRequisitionItem, $this>
     */
    public function purchaseRequisitionItems(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class, 'budget_account_id');
    }
}
