<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Campus\Enums\CourseOfferingLecturerRole;
use Modules\Core\Models\Concerns\BelongsToTenant;

class CourseOfferingLecturer extends Model
{
    use BelongsToTenant;

    protected $table = 'course_offering_lecturers';

    protected $fillable = [
        'tenant_id',
        'course_offering_id',
        'lecturer_id',
        'role',
        'is_active',
        'assigned_at',
        'removed_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => CourseOfferingLecturerRole::class,
            'is_active' => 'boolean',
            'assigned_at' => 'datetime',
            'removed_at' => 'datetime',
        ];
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
