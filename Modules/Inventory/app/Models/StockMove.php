<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Finance\Models\JournalEntry;
use Modules\Inventory\Enums\StockMoveStatus;
use Modules\Inventory\Enums\StockMoveType;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class StockMove extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'warehouse_id',
        'stock_item_id',
        'move_number',
        'move_type',
        'status',
        'quantity',
        'unit_cost',
        'total_cost',
        'reference_type',
        'reference_id',
        'journal_entry_id',
        'moved_at',
        'committed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'move_type' => StockMoveType::class,
            'status' => StockMoveStatus::class,
            'quantity' => 'decimal:4',
            'unit_cost' => 'decimal:4',
            'total_cost' => 'decimal:2',
            'moved_at' => 'datetime',
            'committed_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
