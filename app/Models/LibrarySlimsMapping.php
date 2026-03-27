<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibrarySlimsMapping extends Model
{
    protected $fillable = [
        'tenant_id',
        'entity_type',
        'slims_id',
        'fos_id',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'tenant_id' => 'integer',
            'fos_id' => 'integer',
            'meta' => 'array',
        ];
    }
}
