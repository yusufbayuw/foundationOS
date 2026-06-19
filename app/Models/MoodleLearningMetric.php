<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoodleLearningMetric extends Model
{
    protected $fillable = [
        'tenant_id',
        'fos_user_id',
        'fos_course_id',
        'moodle_user_id',
        'moodle_course_id',
        'metric_type',
        'payload',
        'pulled_at',
    ];

    protected function casts(): array
    {
        return [
            'tenant_id' => 'integer',
            'fos_user_id' => 'integer',
            'fos_course_id' => 'integer',
            'moodle_user_id' => 'integer',
            'moodle_course_id' => 'integer',
            'payload' => 'array',
            'pulled_at' => 'datetime',
        ];
    }
}
