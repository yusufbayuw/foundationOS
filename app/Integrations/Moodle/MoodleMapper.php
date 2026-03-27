<?php

namespace App\Integrations\Moodle;

use Illuminate\Support\Str;
use Modules\Campus\Models\Course;
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

    public function cohortIdnumber(int $tenantId): string
    {
        return "fos_cohort_tenant_{$tenantId}";
    }

    public function mapTenantCategory(Tenant $tenant): array
    {
        return [
            'name' => $tenant->name,
            'idnumber' => $this->tenantCategoryIdnumber((int) $tenant->id),
            'parent' => $this->categoryParent(),
        ];
    }

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

    public function mapEnrollment(int $moodleUserId, int $moodleCourseId, ?int $roleId = null): array
    {
        return [
            'roleid' => $roleId ?? (int) config('moodle.enrol_role_id', 5),
            'userid' => $moodleUserId,
            'courseid' => $moodleCourseId,
        ];
    }

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
        $value = Str::slug($tenantCode . '-' . $courseCode, '-');

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
}
