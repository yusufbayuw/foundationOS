<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;

class Thesis extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'collage_student_id',
        'advisor_lecturer_id',
        'examiner_lecturer_id',
        'title',
        'research_area',
        'proposal_submitted_at',
        'defense_date',
        'status',
        'grade_letter',
        'grade_point',
        'document_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'proposal_submitted_at' => 'datetime',
            'defense_date' => 'datetime',
            'grade_point' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function collageStudent(): BelongsTo
    {
        return $this->belongsTo(CollageStudent::class);
    }

    public function advisorLecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'advisor_lecturer_id');
    }

    public function examinerLecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_lecturer_id');
    }
}
