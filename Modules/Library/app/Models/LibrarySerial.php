<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;

class LibrarySerial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'book_id',
        'frequency_id',
        'title',
        'issn',
        'start_date',
        'period',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'is_active' => 'boolean',
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

    public function frequency(): BelongsTo
    {
        return $this->belongsTo(LibraryFrequency::class, 'frequency_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(LibrarySerialIssue::class, 'serial_id');
    }
}
