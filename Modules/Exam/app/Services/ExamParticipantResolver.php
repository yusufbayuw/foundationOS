<?php

namespace Modules\Exam\Services;

use App\Support\TypedValue;
use Illuminate\Support\Facades\DB;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamToken;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;

class ExamParticipantResolver
{
    public function __construct(
        protected ExamTokenService $tokenService,
    ) {}

    /**
     * @return array{created: int, skipped: int, errors: list<string>}
     */
    public function resolveFromSchoolClass(ExamDefinition $exam): array
    {
        if ($exam->school_class_reference === null) {
            return ['created' => 0, 'skipped' => 0, 'errors' => ['School class reference is required.']];
        }

        $classStudents = ClassStudent::withoutTenantScope()
            ->where('tenant_id', $exam->tenant_id)
            ->where('class_id', $exam->school_class_reference)
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', 'active'))
            ->with(['student.user'])
            ->get();

        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($classStudents as $classStudent) {
            $student = $classStudent->student;

            if ($student === null) {
                $errors[] = "Class student {$classStudent->id} has no linked student.";

                continue;
            }

            $result = $this->upsertParticipant($exam, [
                'participant_source' => ParticipantSource::SchoolStudent,
                'school_student_reference' => $student->id,
                'participant_legacy_id' => $student->id,
                'context_reference_type' => Student::class,
                'student_name' => $student->user->name ?? 'Student '.$student->id,
                'student_identifier' => $student->nis ?? $student->nisn,
                'email' => $student->user?->email,
            ]);

            $created += $result === 'created' ? 1 : 0;
            $skipped += $result === 'skipped' ? 1 : 0;
        }

        return compact('created', 'skipped', 'errors');
    }

    /**
     * @return array{created: int, skipped: int, errors: list<string>}
     */
    public function resolveFromCampusClass(ExamDefinition $exam): array
    {
        if ($exam->campus_class_reference === null) {
            return ['created' => 0, 'skipped' => 0, 'errors' => ['Campus class reference is required.']];
        }

        $items = StudyPlanItem::withoutTenantScope()
            ->where('tenant_id', $exam->tenant_id)
            ->where('course_offering_id', $exam->campus_class_reference)
            ->with(['studyPlan.collageStudent'])
            ->get();

        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            $campusStudent = $item->studyPlan?->collageStudent;

            if ($campusStudent === null) {
                continue;
            }

            $result = $this->upsertParticipant($exam, [
                'participant_source' => ParticipantSource::CampusStudent,
                'campus_student_reference' => $campusStudent->id,
                'participant_legacy_id' => $campusStudent->id,
                'context_reference_type' => CollageStudent::class,
                'student_name' => $campusStudent->full_name,
                'student_identifier' => $campusStudent->student_number ?? $campusStudent->national_student_number,
                'email' => $campusStudent->email,
            ]);

            $created += $result === 'created' ? 1 : 0;
            $skipped += $result === 'skipped' ? 1 : 0;
        }

        return compact('created', 'skipped', 'errors');
    }

    /**
     * @param  list<int>  $userIds
     * @return array{created: int, skipped: int, errors: list<string>}
     */
    public function resolveFromUsers(ExamDefinition $exam, array $userIds): array
    {
        $created = 0;
        $skipped = 0;
        $errors = [];

        $users = User::query()->whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $result = $this->upsertParticipant($exam, [
                'participant_source' => ParticipantSource::User,
                'user_reference' => $user->id,
                'participant_legacy_id' => $user->id,
                'context_reference_type' => User::class,
                'student_name' => $user->name,
                'student_identifier' => (string) $user->id,
                'email' => $user->email,
            ]);

            $created += $result === 'created' ? 1 : 0;
            $skipped += $result === 'skipped' ? 1 : 0;
        }

        return compact('created', 'skipped', 'errors');
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array{created: int, skipped: int, errors: list<string>}
     */
    public function importManualParticipants(ExamDefinition $exam, array $rows): array
    {
        return $this->importRows($exam, $rows, ParticipantSource::Manual);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array{created: int, skipped: int, errors: list<string>}
     */
    public function importFromCsvRows(ExamDefinition $exam, array $rows): array
    {
        return $this->importRows($exam, $rows, ParticipantSource::Import);
    }

    public function generateToken(ExamParticipant $participant): ExamToken
    {
        return $this->tokenService->generateToken($participant);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array{created: int, skipped: int, errors: list<string>}
     */
    protected function importRows(ExamDefinition $exam, array $rows, ParticipantSource $source): array
    {
        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $name = trim(TypedValue::string($row['student_name'] ?? ''));

            if ($name === '') {
                $errors[] = 'Row '.($index + 1).': student_name is required.';

                continue;
            }

            $identifier = trim(TypedValue::string($row['student_identifier'] ?? '')) ?: null;

            if ($identifier !== null && $this->identifierExists($exam, $identifier)) {
                $skipped++;

                continue;
            }

            $result = $this->upsertParticipant($exam, [
                'participant_source' => $source,
                'student_name' => $name,
                'student_identifier' => $identifier,
                'email' => $row['email'] ?? null,
            ]);

            $created += $result === 'created' ? 1 : 0;
            $skipped += $result === 'skipped' ? 1 : 0;
        }

        return compact('created', 'skipped', 'errors');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function upsertParticipant(
        ExamDefinition $exam,
        array $attributes,
        bool $issueToken = true,
        bool $allowDuplicateIdentifier = false,
    ): string {
        $existing = $this->findExisting($exam, $attributes);

        if ($existing !== null) {
            return 'skipped';
        }

        return DB::transaction(function () use ($exam, $attributes, $issueToken): string {
            $participant = ExamParticipant::withoutTenantScope()->make();
            $participant->fill(array_merge([
                'tenant_id' => $exam->tenant_id,
                'exam_definition_id' => $exam->id,
                'status' => ParticipantStatus::Assigned,
                'assigned_at' => now(),
            ], $attributes));
            $participant->save();

            if ($issueToken) {
                $this->tokenService->generateToken($participant);
            }

            return 'created';
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function findExisting(ExamDefinition $exam, array $attributes): ?ExamParticipant
    {
        $query = ExamParticipant::withoutTenantScope()
            ->where('exam_definition_id', $exam->id);

        if (isset($attributes['school_student_reference'])) {
            return $query->clone()->where('school_student_reference', $attributes['school_student_reference'])->first();
        }

        if (isset($attributes['campus_student_reference'])) {
            return $query->clone()->where('campus_student_reference', $attributes['campus_student_reference'])->first();
        }

        if (isset($attributes['user_reference'])) {
            return $query->clone()->where('user_reference', $attributes['user_reference'])->first();
        }

        if (isset($attributes['student_identifier']) && $attributes['student_identifier'] !== '') {
            return $query->clone()->where('student_identifier', $attributes['student_identifier'])->first();
        }

        return null;
    }

    protected function identifierExists(ExamDefinition $exam, string $identifier): bool
    {
        return ExamParticipant::withoutTenantScope()
            ->where('exam_definition_id', $exam->id)
            ->where('student_identifier', $identifier)
            ->exists();
    }

    /**
     * @return array{created: int, skipped: int, errors: list<string>}
     */
    public function syncForExam(ExamDefinition $exam): array
    {
        return match ($exam->exam_academic_context) {
            ExamAcademicContext::School => $exam->school_class_reference
                ? $this->resolveFromSchoolClass($exam)
                : ['created' => 0, 'skipped' => 0, 'errors' => ['School class reference is not set on this exam.']],
            ExamAcademicContext::Campus => $exam->campus_class_reference
                ? $this->resolveFromCampusClass($exam)
                : ['created' => 0, 'skipped' => 0, 'errors' => ['Campus class reference is not set on this exam.']],
            ExamAcademicContext::Standalone => [
                'created' => 0,
                'skipped' => $exam->examParticipants()->count(),
                'errors' => ['Standalone exams use manual or CSV import for participants.'],
            ],
        };
    }
}
