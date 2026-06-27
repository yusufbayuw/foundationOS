<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Campus\Models\CollageStudent;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Services\ExamTokenService;
use Modules\School\Models\Student;

class ExamParticipant extends ExamModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'participant_source',
        'user_reference',
        'school_student_reference',
        'campus_student_reference',
        'student_name',
        'email',
        'student_identifier',
        'context_reference_type',
        'participant_legacy_id',
        'participant_uuid',
        'metadata_json',
        'status',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'participant_source' => ParticipantSource::class,
            'status' => ParticipantStatus::class,
            'metadata_json' => 'array',
            'assigned_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ExamDefinition, $this>
     */
    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function schoolStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'school_student_reference');
    }

    /**
     * @return BelongsTo<CollageStudent, $this>
     */
    public function campusStudent(): BelongsTo
    {
        return $this->belongsTo(CollageStudent::class, 'campus_student_reference');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_reference');
    }

    /**
     * @return HasMany<ExamToken, $this>
     */
    public function examTokens(): HasMany
    {
        return $this->hasMany(ExamToken::class);
    }

    /**
     * @return HasOne<ExamToken, $this>
     */
    public function activeToken(): HasOne
    {
        return $this->hasOne(ExamToken::class)->where('is_active', true)->latestOfMany();
    }

    /**
     * @return HasMany<ExamAttemptSync, $this>
     */
    public function examAttemptSyncs(): HasMany
    {
        return $this->hasMany(ExamAttemptSync::class);
    }

    /**
     * @return HasMany<ExamAttempt, $this>
     */
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * @return HasMany<ExamResult, $this>
     */
    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function issueToken(): ExamToken
    {
        return app(ExamTokenService::class)->generateToken($this);
    }

    /** @return Attribute<string, never> */
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->student_name,
            set: fn (?string $value): array => ['student_name' => $value],
        );
    }

    /** @return Attribute<?string, never> */
    protected function participantCode(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->student_identifier,
            set: fn (?string $value): array => ['student_identifier' => $value],
        );
    }

    /** @return Attribute<?int, never> */
    protected function contextReferenceId(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int => $this->participant_legacy_id,
            set: fn (?int $value): array => ['participant_legacy_id' => $value],
        );
    }

    /** @return Attribute<?string, never> */
    protected function contextReferenceUuid(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->participant_uuid,
            set: fn (?string $value): array => ['participant_uuid' => $value],
        );
    }
}
