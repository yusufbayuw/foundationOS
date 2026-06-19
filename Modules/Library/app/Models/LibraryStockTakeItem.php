<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\User;

class LibraryStockTakeItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stock_take_id',
        'book_copy_id',
        'scanned_by',
        'scanned_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }

    public function stockTake(): BelongsTo
    {
        return $this->belongsTo(LibraryStockTake::class, 'stock_take_id');
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
