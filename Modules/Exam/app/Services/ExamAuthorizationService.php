<?php

namespace Modules\Exam\Services;

use App\Support\CurrentTenant;
use BezhanSalleh\FilamentShield\Support\Utils as ShieldUtils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\Lecturer;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Enums\ExamRole;
use Modules\Exam\Models\ExamDefinition;
use Modules\School\Models\Teacher;

class ExamAuthorizationService
{
    public function __construct(
        protected CurrentTenant $currentTenant,
    ) {}

    public function authorize(AuthUser $user, ExamDefinition $exam, string $permission): bool
    {
        if (! $this->examBelongsToActiveTenant($exam)) {
            return false;
        }

        if ($user instanceof User && $user->isGlobalSuperAdmin()) {
            return true;
        }

        if (! $user->can($permission)) {
            return false;
        }

        if ($this->hasUnrestrictedExamAccess($user)) {
            return true;
        }

        return $this->passesContextualScope($user, $exam);
    }

    public function canViewAny(AuthUser $user): bool
    {
        if ($user instanceof User && $user->isGlobalSuperAdmin()) {
            return true;
        }

        return $user->can(ExamPermission::ViewExam->value)
            && $user->can('ViewAny:ExamDefinition');
    }

    public function canOpenControlRoom(AuthUser $user, ExamDefinition $exam): bool
    {
        if (! $this->authorize($user, $exam, ExamPermission::OpenControlRoom->value)) {
            return false;
        }

        if ($this->hasUnrestrictedExamAccess($user)) {
            return true;
        }

        return $this->isProctorFor($user, $exam);
    }

    /**
     * @param  Builder<ExamDefinition>  $query
     * @return Builder<ExamDefinition>
     */
    public function scopeAccessibleExams(Builder $query, ?AuthUser $user): Builder
    {
        if ($user === null) {
            return $query->whereRaw('0 = 1');
        }

        if ($user instanceof User && $user->isGlobalSuperAdmin()) {
            return $query;
        }

        if ($this->hasUnrestrictedExamAccess($user)) {
            return $query;
        }

        $userId = $user->getKey();
        $teacherIds = $user instanceof User
            ? $user->teachers()->pluck('id')->all()
            : [];
        $lecturerIds = $user instanceof User
            ? $user->lecturers()->pluck('id')->all()
            : [];

        return $query->where(function (Builder $scoped) use ($userId, $teacherIds, $lecturerIds): void {
            $scoped->where('owner_user_id', $userId);

            if ($teacherIds !== []) {
                $scoped->orWhere(function (Builder $school) use ($teacherIds): void {
                    $school->where('exam_academic_context', ExamAcademicContext::School)
                        ->whereIn('school_teacher_reference', $teacherIds);
                });
            }

            if ($lecturerIds !== []) {
                $scoped->orWhere(function (Builder $campus) use ($lecturerIds): void {
                    $campus->where('exam_academic_context', ExamAcademicContext::Campus)
                        ->whereIn('campus_lecturer_reference', $lecturerIds);
                });
            }

            $scoped->orWhereJsonContains('metadata_json->proctor_user_ids', $userId);
        });
    }

    public function examBelongsToActiveTenant(ExamDefinition $exam): bool
    {
        $tenantId = $this->currentTenant->id();

        if ($tenantId === null) {
            return true;
        }

        return (int) $exam->tenant_id === (int) $tenantId;
    }

    public function hasUnrestrictedExamAccess(User $user): bool
    {
        if ($user->hasRole(ShieldUtils::getSuperAdminName())) {
            return true;
        }

        return $user->hasRole([
            ExamRole::ExamAdmin->value,
            ExamRole::SuperAdmin->value,
        ]);
    }

    public function passesContextualScope(User $user, ExamDefinition $exam): bool
    {
        if ((int) $exam->owner_user_id === (int) $user->getKey()) {
            return true;
        }

        if ($user->hasRole(ExamRole::Viewer->value)) {
            return true;
        }

        if ($user->hasRole(ExamRole::Teacher->value) && $this->teacherCanAccess($user, $exam)) {
            return true;
        }

        if ($user->hasRole(ExamRole::Lecturer->value) && $this->lecturerCanAccess($user, $exam)) {
            return true;
        }

        if ($user->hasRole(ExamRole::Proctor->value) && $this->isProctorFor($user, $exam)) {
            return true;
        }

        return false;
    }

    public function isProctorFor(AuthUser $user, ExamDefinition $exam): bool
    {
        $proctorIds = $exam->metadata_json['proctor_user_ids'] ?? [];

        if (! is_array($proctorIds)) {
            return false;
        }

        return in_array((int) $user->getKey(), array_map('intval', $proctorIds), true);
    }

    protected function teacherCanAccess(AuthUser $user, ExamDefinition $exam): bool
    {
        if ($exam->exam_academic_context !== ExamAcademicContext::School) {
            return false;
        }

        if ($exam->school_teacher_reference === null) {
            return false;
        }

        if (! $user instanceof User) {
            return false;
        }

        return Teacher::query()
            ->where('user_id', $user->getKey())
            ->where('id', $exam->school_teacher_reference)
            ->exists();
    }

    protected function lecturerCanAccess(AuthUser $user, ExamDefinition $exam): bool
    {
        if ($exam->exam_academic_context !== ExamAcademicContext::Campus) {
            return false;
        }

        if ($exam->campus_lecturer_reference === null) {
            return false;
        }

        if (! $user instanceof User) {
            return false;
        }

        return Lecturer::query()
            ->where('user_id', $user->getKey())
            ->where('id', $exam->campus_lecturer_reference)
            ->exists();
    }
}
