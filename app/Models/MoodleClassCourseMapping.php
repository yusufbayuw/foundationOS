<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoodleClassCourseMapping extends Model
{
    protected $fillable = [
        'tenant_id',
        'class_id',
        'course_id',
        'moodle_course_id',
        'moodle_course_idnumber',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tenant_id' => 'integer',
            'class_id' => 'integer',
            'course_id' => 'integer',
            'moodle_course_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
