<?php

namespace App\Integrations\Moodle;

use App\Support\TypedValue;
use Illuminate\Support\Str;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class MoodleMapper
{
    public function tenantCategoryIdnumber(int $tenantId): string
    {
        return "fos_tenant_{$tenantId}";
    }

    public function userIdnumber(int $userId): string
    {
        return "fos_user_{$userId}";
    }

    public function courseIdnumber(int $courseId): string
    {
        return "fos_course_{$courseId}";
    }

    public function courseTemplateCategoryIdnumber(int $tenantId): string
    {
        return "fos_template_{$tenantId}";
    }

    public function semesterCategoryIdnumber(int $tenantId, int $academicPeriodId): string
    {
        return "fos_semester_t{$tenantId}_p{$academicPeriodId}";
    }

    public function courseOfferingIdnumber(int $offeringId): string
    {
        return "fos_offering_{$offeringId}";
    }

    public function cohortIdnumber(int $tenantId): string
    {
        return "fos_cohort_tenant_{$tenantId}";
    }

    /**
     * @return array<string, mixed>
     */
    public function mapTenantCategory(Tenant $tenant): array
    {
        return [
            'name' => $tenant->name,
            'idnumber' => $this->tenantCategoryIdnumber((int) $tenant->id),
            'parent' => $this->categoryParent(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapUser(User $user): array
    {
        [$firstName, $lastName] = $this->splitName((string) $user->name);

        return [
            'idnumber' => $this->userIdnumber((int) $user->id),
            'username' => $this->resolveUsername($user),
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => (string) $user->email,
            'suspended' => $this->shouldSuspendUser($user) ? 1 : 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapCourse(Course $course, int $categoryId): array
    {
        $tenantCode = $course->tenant?->code ?: "tenant{$course->tenant_id}";
        $courseCode = $course->code ?: "course{$course->id}";

        return [
            'idnumber' => $this->courseIdnumber((int) $course->id),
            'fullname' => $course->name,
            'shortname' => $this->shortname($tenantCode, $courseCode),
            'summary' => $course->description ?: '',
            'categoryid' => $categoryId,
            'visible' => $this->shouldHideCourse($course) ? 0 : 1,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapTemplateCategory(Tenant $tenant, int $parentCategoryId): array
    {
        return [
            'name' => "Templates — {$tenant->name}",
            'idnumber' => $this->courseTemplateCategoryIdnumber((int) $tenant->id),
            'parent' => $parentCategoryId,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapSemesterCategory(Tenant $tenant, AcademicPeriod $period, int $parentCategoryId): array
    {
        $periodLabel = $period->code ?: $period->name ?: "period{$period->id}";

        return [
            'name' => "{$periodLabel} — {$tenant->name}",
            'idnumber' => $this->semesterCategoryIdnumber((int) $tenant->id, (int) $period->id),
            'parent' => $parentCategoryId,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapCourseOffering(CourseOffering $offering, int $categoryId, string $prerequisiteHint = ''): array
    {
        $course = $offering->course;
        $tenantCode = $course?->tenant?->code ?: "tenant{$offering->tenant_id}";
        $offeringCode = $offering->class_code ?: "offering{$offering->id}";
        $courseCode = $course?->code ?: "course{$offering->course_id}";
        $courseName = $course?->name ?: "Course #{$offering->course_id}";

        $periodLabel = $offering->academicPeriod?->code ?: $offering->academicPeriod?->name ?: '';
        $fullname = trim("{$courseName} — {$offeringCode}".($periodLabel !== '' ? " ({$periodLabel})" : ''));

        $summary = trim(($course?->description ?: '').($prerequisiteHint !== '' ? "\n\n{$prerequisiteHint}" : ''));

        return [
            'idnumber' => $this->courseOfferingIdnumber((int) $offering->id),
            'fullname' => $fullname,
            'shortname' => $this->shortname($tenantCode, "{$courseCode}-{$offeringCode}-{$offering->id}"),
            'summary' => $summary,
            'categoryid' => $categoryId,
            'visible' => $this->shouldHideOffering($offering) ? 0 : 1,
            'startdate' => $offering->academicPeriod?->start_date?->timestamp ?: 0,
            'enddate' => $offering->academicPeriod?->end_date?->timestamp ?: 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapEnrollment(int $moodleUserId, int $moodleCourseId, ?int $roleId = null): array
    {
        return [
            'roleid' => $roleId ?? TypedValue::int(config('moodle.enrol_role_id'), 5),
            'userid' => $moodleUserId,
            'courseid' => $moodleCourseId,
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function splitName(string $name): array
    {
        $clean = trim(preg_replace('/\s+/', ' ', $name) ?? '');

        if ($clean === '') {
            return ['User', '-'];
        }

        $parts = explode(' ', $clean);
        $firstName = array_shift($parts) ?: 'User';
        $lastName = count($parts) > 0 ? implode(' ', $parts) : '-';

        return [$firstName, $lastName];
    }

    protected function resolveUsername(User $user): string
    {
        $username = trim((string) $user->username);

        if ($username !== '') {
            return Str::lower($username);
        }

        return "fos_{$user->id}";
    }

    protected function shortname(string $tenantCode, string $courseCode): string
    {
        $value = Str::slug($tenantCode.'-'.$courseCode, '-');

        return Str::limit($value, 100, '');
    }

    protected function categoryParent(): int
    {
        $parent = config('moodle.category_parent_id');

        if (is_numeric($parent) && (int) $parent >= 0) {
            return (int) $parent;
        }

        return 0;
    }

    protected function shouldSuspendUser(User $user): bool
    {
        if ($user->trashed()) {
            return true;
        }

        return strtolower((string) $user->status) !== 'active';
    }

    protected function shouldHideCourse(Course $course): bool
    {
        if ($course->trashed()) {
            return true;
        }

        return ! (bool) $course->is_active;
    }

    protected function shouldHideOffering(CourseOffering $offering): bool
    {
        if ($offering->trashed()) {
            return true;
        }

        $status = strtolower((string) $offering->status);

        return in_array($status, ['cancelled', 'archived', 'closed'], true);
    }
}
