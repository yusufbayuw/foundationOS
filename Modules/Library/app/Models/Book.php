<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'book_category_id',
        'isbn',
        'isbn13',
        'title',
        'subtitle',
        'authors',
        'publisher',
        'publication_year',
        'publication_place',
        'edition',
        'volume',
        'series',
        'language',
        'pages',
        'dimensions',
        'weight_grams',
        'binding_type',
        'classification_code',
        'keywords',
        'synopsis',
        'cover_image',
        'preview_url',
        'purchase_price',
        'source',
        'total_copies',
        'available_copies',
        'location_shelf',
        'is_active',
        'is_reference_only',
    ];

    protected function casts(): array
    {
        return [
            'authors' => 'array',
            'pages' => 'integer',
            'weight_grams' => 'integer',
            'keywords' => 'array',
            'purchase_price' => 'decimal:2',
            'total_copies' => 'integer',
            'available_copies' => 'integer',
            'is_active' => 'boolean',
            'is_reference_only' => 'boolean',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function fileUploads(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
