<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoodleEntityMapping extends Model
{
    protected $fillable = [
        'entity_type',
        'fos_entity_id',
        'tenant_id',
        'moodle_id',
        'moodle_idnumber',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'moodle_id' => 'integer',
            'meta' => 'array',
        ];
    }
}
