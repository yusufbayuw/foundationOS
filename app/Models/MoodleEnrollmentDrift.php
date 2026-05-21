<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Concerns\BelongsToTenant;

/**
 * Records a discrepancy between FOS-side class enrollment and Moodle-side
 * course enrollment, detected by MoodleEnrollmentReconciler.
 *
 * drift_type values:
 *   - missing_in_fos     → enrolled in Moodle, not in FOS class_students
 *   - missing_in_moodle  → in FOS class_students, not enrolled in Moodle
 *   - mismatched_role    → enrolled in both, but role differs
 */
class MoodleEnrollmentDrift extends Model
{
    use BelongsToTenant;

    public const TYPE_MISSING_IN_FOS = 'missing_in_fos';

    public const TYPE_MISSING_IN_MOODLE = 'missing_in_moodle';

    public const TYPE_MISMATCHED_ROLE = 'mismatched_role';

    protected $fillable = [
        'tenant_id',
        'class_id',
        'course_moodle_id',
        'course_moodle_idnumber',
        'user_id',
        'user_moodle_id',
        'user_moodle_idnumber',
        'drift_type',
        'expected_role',
        'actual_role',
        'detected_at',
        'resolved_at',
        'resolution',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
            'meta' => 'array',
        ];
    }
}
