<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class StudentAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'academic_year_id',
        'student_id',
        'achievement_type_id',
        'title',
        'description',
        'event_name',
        'event_date',
        'event_location',
        'organizer',
        'rank_position',
        'certificate_number',
        'certificate_file',
        'photo_files',
        'news_link',
        'points_earned',
        'verified_by',
        'verified_at',
        'notes',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'photo_files' => 'array',
            'points_earned' => 'integer',
            'verified_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function achievementType(): BelongsTo
    {
        return $this->belongsTo(AchievementType::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
