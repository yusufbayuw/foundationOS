<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Database\Factories\HomepageSectionFactory;
use Modules\Core\Models\Concerns\BelongsToTenant;

class HomepageSection extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    public const TYPE_FEATURED_DONATIONS = 'featured_donations';

    public const TYPE_FEATURED_EVENTS = 'featured_events';

    public const TYPE_PROMO_BANNERS = 'promo_banners';

    /**
     * @return array<int, string>
     */
    public static function standardTypes(): array
    {
        return [
            self::TYPE_FEATURED_DONATIONS,
            self::TYPE_FEATURED_EVENTS,
            self::TYPE_PROMO_BANNERS,
        ];
    }

    protected static function newFactory(): HomepageSectionFactory
    {
        return HomepageSectionFactory::new();
    }

    protected $fillable = [
        'tenant_id',
        'type',
        'title',
        'sort_order',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }
}
