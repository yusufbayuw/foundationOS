<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;

class Book extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

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
        'publisher_id',
        'publication_year',
        'publication_place',
        'edition',
        'volume',
        'series',
        'language',
        'gmd_id',
        'collection_type_id',
        'frequency_id',
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

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<BookCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    /**
     * @return HasMany<BookCopy, $this>
     */
    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    /**
     * @return BelongsTo<LibraryPublisher, $this>
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(LibraryPublisher::class, 'publisher_id');
    }

    /**
     * @return BelongsTo<LibraryGmd, $this>
     */
    public function gmd(): BelongsTo
    {
        return $this->belongsTo(LibraryGmd::class, 'gmd_id');
    }

    /**
     * @return BelongsTo<LibraryCollectionType, $this>
     */
    public function collectionType(): BelongsTo
    {
        return $this->belongsTo(LibraryCollectionType::class, 'collection_type_id');
    }

    /**
     * @return BelongsTo<LibraryFrequency, $this>
     */
    public function frequency(): BelongsTo
    {
        return $this->belongsTo(LibraryFrequency::class, 'frequency_id');
    }

    /**
     * @return BelongsToMany<LibraryAuthor, $this>
     */
    public function authorItems(): BelongsToMany
    {
        return $this->belongsToMany(LibraryAuthor::class, 'book_author', 'book_id', 'author_id');
    }

    /**
     * @return BelongsToMany<LibrarySubject, $this>
     */
    public function subjectItems(): BelongsToMany
    {
        return $this->belongsToMany(LibrarySubject::class, 'book_subject', 'book_id', 'subject_id');
    }

    /**
     * @return HasMany<BookReservation, $this>
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }

    /**
     * @return MorphMany<AuditLog, $this>
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * @return MorphMany<FileUpload, $this>
     */
    public function fileUploads(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
