<?php

namespace Modules\Enrollment\Models;

use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;

class ExamResult extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected ?int $previousExamScheduleId = null;

    protected ?int $previousApplicantId = null;

    protected static function booted(): void
    {
        static::updating(function (self $examResult): void {
            $examResult->previousExamScheduleId = TypedValue::nullableInt($examResult->getOriginal('exam_schedule_id'));
            $examResult->previousApplicantId = TypedValue::nullableInt($examResult->getOriginal('applicant_id'));
        });

        static::saved(function (self $examResult): void {
            if ($examResult->exam_schedule_id) {
                ExamSchedule::find($examResult->exam_schedule_id)?->synchronizeCounters();
            }

            Applicant::find($examResult->applicant_id)?->synchronizeAssessmentSummary();

            $originalExamScheduleId = $examResult->previousExamScheduleId ?? null;
            $originalApplicantId = $examResult->previousApplicantId ?? null;

            if ($originalExamScheduleId && $originalExamScheduleId !== $examResult->exam_schedule_id) {
                ExamSchedule::find($originalExamScheduleId)?->synchronizeCounters();
            }

            if ($originalApplicantId && $originalApplicantId !== $examResult->applicant_id) {
                Applicant::find($originalApplicantId)?->synchronizeAssessmentSummary();
            }
        });

        static::deleted(function (self $examResult): void {
            if ($examResult->exam_schedule_id) {
                ExamSchedule::find($examResult->exam_schedule_id)?->synchronizeCounters();
            }

            Applicant::find($examResult->applicant_id)?->synchronizeAssessmentSummary();
        });
    }

    protected $fillable = [
        'tenant_id',
        'applicant_id',
        'exam_schedule_id',
        'examiner_id',
        'seat_number',
        'score',
        'score_components',
        'grade',
        'is_passed',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'score_components' => 'array',
            'is_passed' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Applicant, $this>
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * @return BelongsTo<ExamSchedule, $this>
     */
    public function examSchedule(): BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }
}
