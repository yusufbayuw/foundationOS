<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Database\Factories\PageFactory;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }

    protected $fillable = [
        'tenant_id',
        'site_id',
        'slug',
        'title_id',
        'title_en',
        'template',
        'status',
        'meta_title',
        'meta_description',
        'og_image',
        'publish_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Site, $this>
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * @return HasMany<PageBlock, $this>
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('sort_order');
    }
}
