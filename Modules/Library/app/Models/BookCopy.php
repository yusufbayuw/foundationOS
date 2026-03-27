<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;

class BookCopy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'book_id',
        'copy_number',
        'barcode',
        'acquisition_date',
        'acquisition_source',
        'price',
        'condition',
        'status',
        'location_shelf',
        'location_id',
        'item_status_id',
        'collection_type_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
            'price' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(LibraryLocation::class, 'location_id');
    }

    public function itemStatus(): BelongsTo
    {
        return $this->belongsTo(LibraryItemStatus::class, 'item_status_id');
    }

    public function collectionType(): BelongsTo
    {
        return $this->belongsTo(LibraryCollectionType::class, 'collection_type_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
