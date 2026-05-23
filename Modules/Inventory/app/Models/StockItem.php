<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Inventory\Enums\ValuationMethod;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;
use Modules\Procurement\Models\ProcurementItem;

class StockItem extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'procurement_item_id',
        'inventory_coa_id',
        'cogs_coa_id',
        'code',
        'name',
        'sku',
        'unit_of_measure',
        'valuation_method',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'valuation_method' => ValuationMethod::class,
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function procurementItem(): BelongsTo
    {
        return $this->belongsTo(ProcurementItem::class);
    }

    public function inventoryCoa(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'inventory_coa_id');
    }

    public function cogsCoa(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'cogs_coa_id');
    }

    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }

    public function stockMoves(): HasMany
    {
        return $this->hasMany(StockMove::class);
    }
}
