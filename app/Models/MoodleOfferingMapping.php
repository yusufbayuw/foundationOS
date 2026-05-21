<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Concerns\BelongsToTenant;

class MoodleOfferingMapping extends Model
{
    use BelongsToTenant;

    public const KIND_TEMPLATE = 'template';

    public const KIND_OFFERING = 'offering';

    protected $fillable = [
        'tenant_id',
        'course_id',
        'course_offering_id',
        'kind',
        'moodle_id',
        'idnumber',
        'is_active',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }
}
