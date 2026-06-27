<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;

class LecturerEvaluation extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'lecturer_id',
        'course_offering_id',
        'score',
        'feedback',
    ];

    /**
     * @return BelongsTo<Lecturer, $this>
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    /**
     * @return BelongsTo<CourseOffering, $this>
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }
}
