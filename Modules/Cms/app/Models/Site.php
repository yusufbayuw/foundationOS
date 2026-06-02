<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Database\Factories\SiteFactory;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Site extends Model
{
    /** @use HasFactory<SiteFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected static function newFactory(): SiteFactory
    {
        return SiteFactory::new();
    }

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'domain',
        'default_locale',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
