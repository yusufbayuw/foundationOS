<?php

namespace App\Integrations\Moodle;

use App\Integrations\Moodle\Exceptions\MoodleIntegrationException;
use App\Integrations\Moodle\Exceptions\MoodleReadonlySkipException;
use App\Models\MoodleClassCourseMapping;
use App\Models\MoodleEntityMapping;
use App\Models\MoodleSyncOutbox;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\CourseOfferingLecturer;
use Modules\Campus\Models\CoursePrerequisite;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;

class MoodleSyncService
{
    public function __construct(
        protected MoodleClient $client,
        protected MoodleMapper $mapper,
    ) {}

    public function syncOutboxItem(MoodleSyncOutbox $outbox): void
    {
        match ($outbox->entity_type) {
            MoodleOutboxService::ENTITY_USER => $this->syncUserOutbox($outbox),
            MoodleOutboxService::ENTITY_COURSE => $this->syncCourseOutbox($outbox),
            MoodleOutboxService::ENTITY_ENROLLMENT => $this->syncEnrollmentOutbox($outbox),
            MoodleOutboxService::ENTITY_LECTURER_ASSIGNMENT => $this->syncLecturerAssignmentOutbox($outbox),
            MoodleOutboxService::ENTITY_COURSE_OFFERING => $this->syncCourseOfferingOutbox($outbox),
            MoodleOutboxService::ENTITY_STUDY_PLAN_ENROLLMENT => $this->syncStudyPlanEnrollmentOutbox($outbox),
            default => throw new MoodleIntegrationException("Unsupported outbox entity type: {$outbox->entity_type}"),
        };
    }

    protected function syncUserOutbox(MoodleSyncOutbox $outbox): void
    {
        $user = User::withTrashed()->find($outbox->entity_id);

        if (! $user) {
            if ($outbox->action === MoodleOutboxService::ACTION_DEACTIVATE) {
                $this->deactivateMissingUser((int) $outbox->entity_id);

                return;
            }

            throw new MoodleIntegrationException("User {$outbox->entity_id} not found.");
        }

        if ($outbox->action === MoodleOutboxService::ACTION_DEACTIVATE) {
            $moodleUserId = $this->upsertUserAsDeactivated($user);
        } else {
            $moodleUserId = $this->upsertUser($user);
        }

        $this->upsertEntityMapping('user', (int) $user->id, null, $moodleUserId, $this->mapper->userIdnumber((int) $user->id));
    }

    protected function syncCourseOutbox(MoodleSyncOutbox $outbox): void
    {
        $course = Course::withTrashed()->with('tenant')->find($outbox->entity_id);

        if (! $course) {
            if ($outbox->action === MoodleOutboxService::ACTION_DEACTIVATE) {
                $this->deactivateMissingCourse((int) $outbox->entity_id);

                return;
            }

            throw new MoodleIntegrationException("Course {$outbox->entity_id} not found.");
        }

        if ($outbox->action === MoodleOutboxService::ACTION_DEACTIVATE) {
            $moodleCourseId = $this->upsertCourseAsHidden($course);
        } else {
            if (! $course->tenant) {
                throw new MoodleIntegrationException("Course {$course->id} has no tenant.");
            }

            $categoryId = $this->upsertTenantCategory($course->tenant);
            $moodleCourseId = $this->upsertCourse($course, $categoryId);
        }

        $this->upsertEntityMapping('course', (int) $course->id, (int) $course->tenant_id, $moodleCourseId, $this->mapper->courseIdnumber((int) $course->id));
    }

    protected function syncEnrollmentOutbox(MoodleSyncOutbox $outbox): void
    {
        $payload = is_array($outbox->payload) ? $outbox->payload : [];
        $classStudentId = (int) $outbox->entity_id;
        $classStudent = ClassStudent::withTrashed()
            ->with([
                'student' => fn ($query) => $query->withTrashed()->with('user'),
            ])
            ->find($classStudentId);

        $classId = (int) ($payload['class_id'] ?? $classStudent?->class_id ?? 0);
        $tenantId = (int) ($payload['tenant_id'] ?? $classStudent?->tenant_id ?? 0);
        $studentId = (int) ($payload['student_id'] ?? $classStudent?->student_id ?? 0);

        if ($classId <= 0 || $tenantId <= 0 || $studentId <= 0) {
            throw new MoodleIntegrationException("Enrollment payload is incomplete for outbox {$outbox->id}.");
        }

        /** @var Student|null $student */
        $student = $classStudent?->student;
        if (! $student) {
            $student = Student::withTrashed()->with('user')->find($studentId);
        }

        if (! $student || ! $student->user) {
            throw new MoodleIntegrationException("Student {$studentId} does not have linked user.");
        }

        $moodleUserId = $this->upsertUser($student->user);
        $moodleCourseId = $this->resolveMoodleCourseIdForClass($tenantId, $classId);
        $this->syncTenantCohortMembership($tenantId, $moodleUserId);
        $roleId = $this->resolveEnrollmentRoleId($student->user, $tenantId);

        if ($outbox->action === MoodleOutboxService::ACTION_UNENROLL) {
            $this->unenrollUser($moodleUserId, $moodleCourseId);

            return;
        }

        $this->enrollUser($moodleUserId, $moodleCourseId, $roleId);
    }

    public function upsertTenantCategory(Tenant $tenant): int
    {
        $idnumber = $this->mapper->tenantCategoryIdnumber((int) $tenant->id);

        $existing = $this->findEntityMapping('tenant_category', (int) $tenant->id, $idnumber);
        $moodleCategoryId = $existing?->moodle_id ?: $this->findMoodleCategoryIdByIdnumber($idnumber);
        $payload = $this->mapper->mapTenantCategory($tenant);

        if (! $moodleCategoryId) {
            $created = $this->callMoodle('core_course_create_categories', [
                'categories' => [$payload],
            ]);

            $moodleCategoryId = (int) Arr::get($created, '0.id', 0);
        } else {
            $this->callMoodle('core_course_update_categories', [
                'categories' => [[
                    'id' => $moodleCategoryId,
                    'name' => $payload['name'],
                    'parent' => $payload['parent'],
                ]],
            ]);
        }

        if ($moodleCategoryId <= 0) {
            throw new MoodleIntegrationException("Unable to resolve Moodle category for tenant {$tenant->id}.");
        }

        $this->upsertEntityMapping('tenant_category', (int) $tenant->id, (int) $tenant->id, $moodleCategoryId, $idnumber);

        return $moodleCategoryId;
    }

    public function upsertUser(User $user): int
    {
        return $this->upsertUserWithSuspendedState($user, null);
    }

    public function upsertUserAsDeactivated(User $user): int
    {
        return $this->upsertUserWithSuspendedState($user, 1);
    }

    protected function upsertUserWithSuspendedState(User $user, ?int $forcedSuspended): int
    {
        $idnumber = $this->mapper->userIdnumber((int) $user->id);
        $existing = $this->findEntityMapping('user', (int) $user->id, $idnumber);
        $moodleUserId = $existing?->moodle_id ?: $this->findMoodleUserIdByIdnumber($idnumber);
        $payload = $this->mapper->mapUser($user);
        $suspended = $forcedSuspended ?? (int) $payload['suspended'];

        if (! $moodleUserId) {
            $createPayload = [
                'idnumber' => $payload['idnumber'],
                'username' => $payload['username'],
                'firstname' => $payload['firstname'],
                'lastname' => $payload['lastname'],
                'email' => $payload['email'],
                'password' => Str::random(20).'Aa1!',
            ];

            try {
                $created = $this->callMoodle('core_user_create_users', [
                    'users' => [$createPayload],
                ]);
            } catch (MoodleIntegrationException $exception) {
                // Handle common collisions (username/email) by retrying with guaranteed-unique identifiers.
                if (! str_contains(strtolower($exception->getMessage()), 'invalidparameter')) {
                    throw $exception;
                }

                $fallbackPayload = $createPayload;
                $fallbackPayload['username'] = 'fos_'.(int) $user->id;
                $fallbackPayload['email'] = $this->buildFallbackMoodleEmail((int) $user->id, (string) $payload['email']);

                $created = $this->callMoodle('core_user_create_users', [
                    'users' => [$fallbackPayload],
                ]);
            }

            $moodleUserId = (int) Arr::get($created, '0.id', 0);

            if ($moodleUserId > 0 && $suspended === 1) {
                $this->callMoodle('core_user_update_users', [
                    'users' => [[
                        'id' => $moodleUserId,
                        'suspended' => 1,
                    ]],
                ]);
            }
        } else {
            $this->callMoodle('core_user_update_users', [
                'users' => [[
                    'id' => $moodleUserId,
                    'firstname' => $payload['firstname'],
                    'lastname' => $payload['lastname'],
                    'email' => $payload['email'],
                    'username' => $payload['username'],
                    'suspended' => $suspended,
                ]],
            ]);
        }

        if ($moodleUserId <= 0) {
            throw new MoodleIntegrationException("Unable to resolve Moodle user for FOS user {$user->id}.");
        }

        $this->upsertEntityMapping('user', (int) $user->id, null, $moodleUserId, $idnumber);

        return $moodleUserId;
    }

    protected function buildFallbackMoodleEmail(int $userId, string $email): string
    {
        $email = trim($email);
        if ($email !== '' && str_contains($email, '@')) {
            [$local, $domain] = explode('@', $email, 2);
            $local = preg_replace('/[^a-zA-Z0-9._+-]/', '', $local) ?: 'user';
            $domain = preg_replace('/[^a-zA-Z0-9.-]/', '', $domain) ?: 'example.invalid';

            return "{$local}+fos{$userId}@{$domain}";
        }

        return "fos_user_{$userId}@example.invalid";
    }

    public function upsertCourse(Course $course, int $categoryId): int
    {
        $idnumber = $this->mapper->courseIdnumber((int) $course->id);
        $existing = $this->findEntityMapping('course', (int) $course->id, $idnumber);
        $moodleCourseId = $existing?->moodle_id ?: $this->findMoodleCourseIdByIdnumber($idnumber);
        $payload = $this->mapper->mapCourse($course, $categoryId);

        if (! $moodleCourseId) {
            $created = $this->callMoodle('core_course_create_courses', [
                'courses' => [$payload],
            ]);

            $moodleCourseId = (int) Arr::get($created, '0.id', 0);
        } else {
            $this->callMoodle('core_course_update_courses', [
                'courses' => [[
                    'id' => $moodleCourseId,
                    'fullname' => $payload['fullname'],
                    'shortname' => $payload['shortname'],
                    'summary' => $payload['summary'],
                    'categoryid' => $payload['categoryid'],
                    'visible' => $payload['visible'],
                ]],
            ]);
        }

        if ($moodleCourseId <= 0) {
            throw new MoodleIntegrationException("Unable to resolve Moodle course for FOS course {$course->id}.");
        }

        $this->upsertEntityMapping('course', (int) $course->id, (int) $course->tenant_id, $moodleCourseId, $idnumber);

        return $moodleCourseId;
    }

    public function upsertCourseAsHidden(Course $course): int
    {
        $idnumber = $this->mapper->courseIdnumber((int) $course->id);
        $existing = $this->findEntityMapping('course', (int) $course->id, $idnumber);
        $moodleCourseId = $existing?->moodle_id ?: $this->findMoodleCourseIdByIdnumber($idnumber);

        if ($moodleCourseId <= 0) {
            throw new MoodleIntegrationException("Unable to resolve Moodle course for FOS course {$course->id}.");
        }

        $this->callMoodle('core_course_update_courses', [
            'courses' => [[
                'id' => $moodleCourseId,
                'visible' => 0,
            ]],
        ]);

        $this->upsertEntityMapping('course', (int) $course->id, (int) $course->tenant_id, $moodleCourseId, $idnumber);

        return $moodleCourseId;
    }

    protected function syncLecturerAssignmentOutbox(MoodleSyncOutbox $outbox): void
    {
        $payload = is_array($outbox->payload) ? $outbox->payload : [];
        $assignmentId = (int) $outbox->entity_id;

        $assignment = CourseOfferingLecturer::withoutTenantScope()
            ->with([
                'courseOffering' => fn ($q) => $q->withoutTenantScope()->withTrashed(),
                'lecturer' => fn ($q) => $q->withoutTenantScope()->withTrashed()->with('user'),
            ])
            ->find($assignmentId);

        $courseOfferingId = (int) ($payload['course_offering_id'] ?? $assignment?->course_offering_id ?? 0);
        $lecturerId = (int) ($payload['lecturer_id'] ?? $assignment?->lecturer_id ?? 0);
        $tenantId = (int) ($payload['tenant_id'] ?? $assignment?->tenant_id ?? 0);
        $role = (string) ($payload['role'] ?? $assignment?->role?->value ?? 'primary');

        if ($courseOfferingId <= 0 || $lecturerId <= 0 || $tenantId <= 0) {
            throw new MoodleIntegrationException("Lecturer assignment payload is incomplete for outbox {$outbox->id}.");
        }

        /** @var Lecturer|null $lecturer */
        $lecturer = $assignment?->lecturer ?: Lecturer::withoutTenantScope()->withTrashed()->with('user')->find($lecturerId);
        if (! $lecturer || ! $lecturer->user) {
            throw new MoodleIntegrationException("Lecturer {$lecturerId} has no linked user.");
        }

        /** @var CourseOffering|null $offering */
        $offering = $assignment?->courseOffering ?: CourseOffering::withoutTenantScope()->withTrashed()->find($courseOfferingId);
        if (! $offering) {
            throw new MoodleIntegrationException("CourseOffering {$courseOfferingId} not found.");
        }

        $moodleUserId = $this->upsertUser($lecturer->user);
        $moodleCourseId = $this->resolveMoodleCourseIdForOffering($offering);
        $this->syncTenantCohortMembership($tenantId, $moodleUserId);

        $roleId = $this->resolveLecturerRoleId($role);

        if ($outbox->action === MoodleOutboxService::ACTION_UNASSIGN) {
            $this->unenrollUser($moodleUserId, $moodleCourseId);

            return;
        }

        $this->enrollUser($moodleUserId, $moodleCourseId, $roleId);
    }

    protected function syncCourseOfferingOutbox(MoodleSyncOutbox $outbox): void
    {
        $offering = CourseOffering::withoutTenantScope()
            ->withTrashed()
            ->with(['course' => fn ($q) => $q->withTrashed()->with('tenant'), 'academicPeriod' => fn ($q) => $q->withTrashed()])
            ->find($outbox->entity_id);

        if (! $offering) {
            if ($outbox->action === MoodleOutboxService::ACTION_DEACTIVATE) {
                $this->deactivateMissingCourseOffering((int) $outbox->entity_id);

                return;
            }

            throw new MoodleIntegrationException("CourseOffering {$outbox->entity_id} not found.");
        }

        if (! $offering->course || ! $offering->course->tenant) {
            throw new MoodleIntegrationException("CourseOffering {$offering->id} has no resolvable course/tenant.");
        }

        if ($outbox->action === MoodleOutboxService::ACTION_DEACTIVATE) {
            $moodleCourseId = $this->upsertCourseOfferingAsHidden($offering);
        } else {
            $tenant = $offering->course->tenant;
            $tenantCategoryId = $this->upsertTenantCategory($tenant);
            $semesterCategoryId = $offering->academicPeriod
                ? $this->upsertSemesterCategory($tenant, $offering->academicPeriod, $tenantCategoryId)
                : $tenantCategoryId;
            $moodleCourseId = $this->upsertCourseOffering($offering, $semesterCategoryId);
        }

        $this->upsertEntityMapping(
            MoodleOutboxService::ENTITY_COURSE_OFFERING,
            (int) $offering->id,
            (int) $offering->tenant_id,
            $moodleCourseId,
            $this->mapper->courseOfferingIdnumber((int) $offering->id),
        );
    }

    public function upsertSemesterCategory(Tenant $tenant, AcademicPeriod $period, int $parentCategoryId): int
    {
        $idnumber = $this->mapper->semesterCategoryIdnumber((int) $tenant->id, (int) $period->id);
        $existing = $this->findEntityMapping('semester_category', (int) $period->id, $idnumber);
        $moodleCategoryId = $existing?->moodle_id ?: $this->findMoodleCategoryIdByIdnumber($idnumber);
        $payload = $this->mapper->mapSemesterCategory($tenant, $period, $parentCategoryId);

        if (! $moodleCategoryId) {
            $created = $this->callMoodle('core_course_create_categories', [
                'categories' => [$payload],
            ]);

            $moodleCategoryId = (int) Arr::get($created, '0.id', 0);
        } else {
            $this->callMoodle('core_course_update_categories', [
                'categories' => [[
                    'id' => $moodleCategoryId,
                    'name' => $payload['name'],
                    'parent' => $payload['parent'],
                ]],
            ]);
        }

        if ($moodleCategoryId <= 0) {
            throw new MoodleIntegrationException("Unable to resolve Moodle semester category for period {$period->id}.");
        }

        $this->upsertEntityMapping('semester_category', (int) $period->id, (int) $tenant->id, $moodleCategoryId, $idnumber);

        return $moodleCategoryId;
    }

    public function upsertCourseOffering(CourseOffering $offering, int $categoryId): int
    {
        $idnumber = $this->mapper->courseOfferingIdnumber((int) $offering->id);
        $existing = $this->findEntityMapping(MoodleOutboxService::ENTITY_COURSE_OFFERING, (int) $offering->id, $idnumber);
        $moodleCourseId = $existing?->moodle_id ?: $this->findMoodleCourseIdByIdnumber($idnumber);
        $prerequisiteHint = $this->buildPrerequisiteHint($offering);
        $payload = $this->mapper->mapCourseOffering($offering, $categoryId, $prerequisiteHint);

        if (! $moodleCourseId) {
            $created = $this->callMoodle('core_course_create_courses', [
                'courses' => [$payload],
            ]);

            $moodleCourseId = (int) Arr::get($created, '0.id', 0);
        } else {
            $this->callMoodle('core_course_update_courses', [
                'courses' => [[
                    'id' => $moodleCourseId,
                    'fullname' => $payload['fullname'],
                    'shortname' => $payload['shortname'],
                    'summary' => $payload['summary'],
                    'categoryid' => $payload['categoryid'],
                    'visible' => $payload['visible'],
                    'startdate' => $payload['startdate'],
                    'enddate' => $payload['enddate'],
                ]],
            ]);
        }

        if ($moodleCourseId <= 0) {
            throw new MoodleIntegrationException("Unable to resolve Moodle course for CourseOffering {$offering->id}.");
        }

        $this->upsertEntityMapping(
            MoodleOutboxService::ENTITY_COURSE_OFFERING,
            (int) $offering->id,
            (int) $offering->tenant_id,
            $moodleCourseId,
            $idnumber,
        );

        return $moodleCourseId;
    }

    public function upsertCourseOfferingAsHidden(CourseOffering $offering): int
    {
        $idnumber = $this->mapper->courseOfferingIdnumber((int) $offering->id);
        $existing = $this->findEntityMapping(MoodleOutboxService::ENTITY_COURSE_OFFERING, (int) $offering->id, $idnumber);
        $moodleCourseId = $existing?->moodle_id ?: $this->findMoodleCourseIdByIdnumber($idnumber);

        if ($moodleCourseId <= 0) {
            throw new MoodleIntegrationException("Unable to resolve Moodle course for CourseOffering {$offering->id}.");
        }

        $this->callMoodle('core_course_update_courses', [
            'courses' => [[
                'id' => $moodleCourseId,
                'visible' => 0,
            ]],
        ]);

        $this->upsertEntityMapping(
            MoodleOutboxService::ENTITY_COURSE_OFFERING,
            (int) $offering->id,
            (int) $offering->tenant_id,
            $moodleCourseId,
            $idnumber,
        );

        return $moodleCourseId;
    }

    protected function deactivateMissingCourseOffering(int $offeringId): void
    {
        $idnumber = $this->mapper->courseOfferingIdnumber($offeringId);
        $existing = $this->findEntityMapping(MoodleOutboxService::ENTITY_COURSE_OFFERING, $offeringId, $idnumber);
        $moodleCourseId = $existing?->moodle_id ?: $this->findMoodleCourseIdByIdnumber($idnumber);

        if ($moodleCourseId <= 0) {
            return;
        }

        $this->callMoodle('core_course_update_courses', [
            'courses' => [[
                'id' => $moodleCourseId,
                'visible' => 0,
            ]],
        ]);

        $this->upsertEntityMapping(MoodleOutboxService::ENTITY_COURSE_OFFERING, $offeringId, null, $moodleCourseId, $idnumber);
    }

    protected function buildPrerequisiteHint(CourseOffering $offering): string
    {
        if (! $offering->course_id) {
            return '';
        }

        $prerequisites = CoursePrerequisite::withoutTenantScope()
            ->where('course_id', $offering->course_id)
            ->with(['prerequisiteCourse' => fn ($q) => $q->withTrashed()])
            ->get();

        if ($prerequisites->isEmpty()) {
            return '';
        }

        $lines = $prerequisites->map(function (CoursePrerequisite $p): string {
            $course = $p->prerequisiteCourse;
            $label = $course ? trim("{$course->code} — {$course->name}") : "course #{$p->prerequisite_course_id}";
            $extras = [];
            if ($p->min_grade !== null) {
                $extras[] = "min grade {$p->min_grade}";
            }
            if (! $p->is_required) {
                $extras[] = 'optional';
            }
            if ($p->note) {
                $extras[] = $p->note;
            }

            return '- '.$label.(count($extras) > 0 ? ' ('.implode(', ', $extras).')' : '');
        })->all();

        return "Prerequisites (FOS-managed):\n".implode("\n", $lines);
    }

    protected function syncStudyPlanEnrollmentOutbox(MoodleSyncOutbox $outbox): void
    {
        $payload = is_array($outbox->payload) ? $outbox->payload : [];
        $itemId = (int) $outbox->entity_id;

        $item = StudyPlanItem::withoutTenantScope()
            ->withTrashed()
            ->with([
                'studyPlan' => fn ($q) => $q->withTrashed()->with(['collageStudent' => fn ($qq) => $qq->withTrashed()->with('user')]),
                'courseOffering' => fn ($q) => $q->withTrashed()->with(['course' => fn ($qq) => $qq->withTrashed()->with('tenant'), 'academicPeriod' => fn ($qq) => $qq->withTrashed()]),
            ])
            ->find($itemId);

        $tenantId = (int) ($payload['tenant_id'] ?? $item?->tenant_id ?? 0);
        $offeringId = (int) ($payload['course_offering_id'] ?? $item?->course_offering_id ?? 0);
        $userId = (int) ($payload['user_id'] ?? $item?->studyPlan?->collageStudent?->user_id ?? 0);

        if ($tenantId <= 0 || $offeringId <= 0 || $userId <= 0) {
            throw new MoodleIntegrationException("StudyPlan enrollment payload incomplete for outbox {$outbox->id}.");
        }

        $offering = $item?->courseOffering ?: CourseOffering::withoutTenantScope()->withTrashed()->find($offeringId);
        if (! $offering) {
            throw new MoodleIntegrationException("CourseOffering {$offeringId} not found.");
        }

        $user = $item?->studyPlan?->collageStudent?->user ?: User::withTrashed()->find($userId);
        if (! $user) {
            throw new MoodleIntegrationException("User {$userId} not found.");
        }

        $moodleUserId = $this->upsertUser($user);
        $moodleCourseId = $this->resolveMoodleCourseIdForOffering($offering);
        $this->syncTenantCohortMembership($tenantId, $moodleUserId);

        if ($outbox->action === MoodleOutboxService::ACTION_UNENROLL) {
            $this->unenrollUser($moodleUserId, $moodleCourseId);

            return;
        }

        $roleId = (int) config('moodle.role_map.student', 5);
        $this->enrollUser($moodleUserId, $moodleCourseId, $roleId);
    }

    public function resolveMoodleCourseIdForOffering(CourseOffering $offering): int
    {
        // DB first: prefer per-semester offering mapping (Fase 6.1).
        $offeringMapping = MoodleEntityMapping::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_COURSE_OFFERING)
            ->where('fos_entity_id', (int) $offering->id)
            ->first();

        if ($offeringMapping?->moodle_id) {
            return (int) $offeringMapping->moodle_id;
        }

        // DB fallback: course-master mapping (legacy).
        $courseMapping = MoodleEntityMapping::query()
            ->where('entity_type', 'course')
            ->where('fos_entity_id', (int) $offering->course_id)
            ->first();

        if ($courseMapping?->moodle_id) {
            return (int) $courseMapping->moodle_id;
        }

        // Remote lookup as last resort.
        $offeringIdnumber = $this->mapper->courseOfferingIdnumber((int) $offering->id);
        $moodleCourseId = $this->findMoodleCourseIdByIdnumber($offeringIdnumber);

        if ($moodleCourseId > 0) {
            return $moodleCourseId;
        }

        $courseIdnumber = $this->mapper->courseIdnumber((int) $offering->course_id);
        $moodleCourseId = $this->findMoodleCourseIdByIdnumber($courseIdnumber);

        if ($moodleCourseId <= 0) {
            throw new MoodleIntegrationException(
                "Unable to resolve Moodle course for CourseOffering {$offering->id} (course {$offering->course_id})."
            );
        }

        return $moodleCourseId;
    }

    public function resolveLecturerRoleId(string $role): int
    {
        return match ($role) {
            'assistant' => (int) config('moodle.role_map.assistant_teacher', 4),
            default => (int) config('moodle.role_map.teacher', 3),
        };
    }

    public function resolveMoodleCourseIdForClass(int $tenantId, int $classId): int
    {
        $mapping = MoodleClassCourseMapping::query()
            ->where('tenant_id', $tenantId)
            ->where('class_id', $classId)
            ->where('is_active', true)
            ->first();

        if (! $mapping) {
            throw new MoodleIntegrationException("No moodle_class_course_mappings found for tenant {$tenantId}, class {$classId}.");
        }

        if ($mapping->moodle_course_id) {
            return (int) $mapping->moodle_course_id;
        }

        if ($mapping->course_id) {
            $courseIdnumber = $this->mapper->courseIdnumber((int) $mapping->course_id);
            $resolved = $this->findMoodleCourseIdByIdnumber($courseIdnumber);

            if ($resolved > 0) {
                $mapping->forceFill(['moodle_course_id' => $resolved])->save();

                return $resolved;
            }
        }

        if ($mapping->moodle_course_idnumber) {
            $resolved = $this->findMoodleCourseIdByIdnumber((string) $mapping->moodle_course_idnumber);

            if ($resolved > 0) {
                $mapping->forceFill(['moodle_course_id' => $resolved])->save();

                return $resolved;
            }
        }

        throw new MoodleIntegrationException("Unable to resolve Moodle course ID for class mapping {$mapping->id}.");
    }

    public function enrollUser(int $moodleUserId, int $moodleCourseId, ?int $roleId = null): void
    {
        try {
            $this->callMoodle('enrol_manual_enrol_users', [
                'enrolments' => [$this->mapper->mapEnrollment($moodleUserId, $moodleCourseId, $roleId)],
            ]);
        } catch (MoodleIntegrationException $exception) {
            if ($this->isAlreadyEnrolledError($exception)) {
                return;
            }

            throw $exception;
        }
    }

    protected function isAlreadyEnrolledError(MoodleIntegrationException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'already enrolled')
            || str_contains($message, 'wsuseralreadyenrolled')
            || str_contains($message, 'user is already enrolled');
    }

    public function unenrollUser(int $moodleUserId, int $moodleCourseId): void
    {
        $this->callMoodle('enrol_manual_unenrol_users', [
            'enrolments' => [[
                'userid' => $moodleUserId,
                'courseid' => $moodleCourseId,
            ]],
        ]);
    }

    public function findMoodleUserIdByIdnumber(string $idnumber): int
    {
        $response = $this->callMoodle('core_user_get_users_by_field', [
            'field' => 'idnumber',
            'values' => [$idnumber],
        ]);

        return (int) Arr::get($response, '0.id', 0);
    }

    public function findMoodleCourseIdByIdnumber(string $idnumber): int
    {
        $response = $this->callMoodle('core_course_get_courses_by_field', [
            'field' => 'idnumber',
            'value' => $idnumber,
        ]);

        return (int) Arr::get($response, 'courses.0.id', 0);
    }

    public function findMoodleCategoryIdByIdnumber(string $idnumber): int
    {
        $response = $this->callMoodle('core_course_get_categories', [
            'criteria' => [
                ['key' => 'idnumber', 'value' => $idnumber],
            ],
        ]);

        return (int) Arr::get($response, '0.id', 0);
    }

    protected function deactivateMissingUser(int $userId): void
    {
        $idnumber = $this->mapper->userIdnumber($userId);
        $existing = $this->findEntityMapping('user', $userId, $idnumber);
        $moodleUserId = $existing?->moodle_id ?: $this->findMoodleUserIdByIdnumber($idnumber);

        if ($moodleUserId <= 0) {
            return;
        }

        $this->callMoodle('core_user_update_users', [
            'users' => [[
                'id' => $moodleUserId,
                'suspended' => 1,
            ]],
        ]);

        $this->upsertEntityMapping('user', $userId, null, $moodleUserId, $idnumber);
    }

    protected function deactivateMissingCourse(int $courseId): void
    {
        $idnumber = $this->mapper->courseIdnumber($courseId);
        $existing = $this->findEntityMapping('course', $courseId, $idnumber);
        $moodleCourseId = $existing?->moodle_id ?: $this->findMoodleCourseIdByIdnumber($idnumber);

        if ($moodleCourseId <= 0) {
            return;
        }

        $this->callMoodle('core_course_update_courses', [
            'courses' => [[
                'id' => $moodleCourseId,
                'visible' => 0,
            ]],
        ]);

        $this->upsertEntityMapping('course', $courseId, null, $moodleCourseId, $idnumber);
    }

    public function ensureTenantCohort(int $tenantId): int
    {
        $idnumber = $this->mapper->cohortIdnumber($tenantId);
        $existing = $this->findEntityMapping('tenant_cohort', $tenantId, $idnumber);

        if ($existing?->moodle_id) {
            return (int) $existing->moodle_id;
        }

        $cohortId = $this->findMoodleCohortIdByIdnumber($idnumber);

        if ($cohortId <= 0) {
            $tenant = Tenant::query()->findOrFail($tenantId);
            try {
                $created = $this->callMoodle('core_cohort_create_cohorts', [
                    'cohorts' => [[
                        'name' => $tenant->name,
                        'idnumber' => $idnumber,
                        'description' => "Auto cohort for tenant {$tenant->name}",
                        'contextid' => 1,
                        'visible' => 1,
                    ]],
                ]);
            } catch (MoodleIntegrationException $exception) {
                // Backward-compatible payload for Moodle variants using cohorttype.
                if (! str_contains(strtolower($exception->getMessage()), 'invalidparameter')) {
                    throw $exception;
                }

                $created = $this->callMoodle('core_cohort_create_cohorts', [
                    'cohorts' => [[
                        'name' => $tenant->name,
                        'idnumber' => $idnumber,
                        'description' => "Auto cohort for tenant {$tenant->name}",
                        'categorytype' => [
                            'type' => 'system',
                            'value' => '0',
                        ],
                        'visible' => 1,
                    ]],
                ]);
            }

            $cohortId = (int) Arr::get($created, '0.id', 0);
        }

        if ($cohortId <= 0) {
            throw new MoodleIntegrationException("Unable to resolve moodle cohort for tenant {$tenantId}.");
        }

        $this->upsertEntityMapping('tenant_cohort', $tenantId, $tenantId, $cohortId, $idnumber);

        return $cohortId;
    }

    public function findMoodleCohortIdByIdnumber(string $idnumber): int
    {
        try {
            $searchResult = $this->callMoodle('core_cohort_search_cohorts', [
                'query' => $idnumber,
                'context' => [
                    'contextlevel' => 'system',
                    'instanceid' => 0,
                ],
                'includes' => 'all',
                'limitfrom' => 0,
                'limitnum' => 25,
            ]);
        } catch (MoodleIntegrationException) {
            return 0;
        }

        foreach (($searchResult['cohorts'] ?? []) as $cohort) {
            if (! is_array($cohort)) {
                continue;
            }

            if (($cohort['idnumber'] ?? null) === $idnumber) {
                return (int) ($cohort['id'] ?? 0);
            }
        }

        return 0;
    }

    public function syncTenantCohortMembership(int $tenantId, int $moodleUserId): void
    {
        if (! config('moodle.cohort_sync_enabled', true)) {
            return;
        }

        $cohortId = $this->ensureTenantCohort($tenantId);

        try {
            $this->callMoodle('core_cohort_add_cohort_members', [
                'members' => [[
                    'cohortid' => $cohortId,
                    'userid' => $moodleUserId,
                ]],
            ]);
        } catch (MoodleIntegrationException $exception) {
            if (! str_contains(strtolower($exception->getMessage()), 'invalidparameter')) {
                throw $exception;
            }

            $this->callMoodle('core_cohort_add_cohort_members', [
                'members' => [[
                    'cohorttype' => [
                        'type' => 'id',
                        'value' => $cohortId,
                    ],
                    'usertype' => [
                        'type' => 'id',
                        'value' => $moodleUserId,
                    ],
                ]],
            ]);
        }
    }

    public function resolveEnrollmentRoleId(User $user, int $tenantId): int
    {
        $defaultRole = (int) config('moodle.role_map.student', config('moodle.enrol_role_id', 5));

        /** @var UserTenantRole|null $assignment */
        $assignment = UserTenantRole::query()
            ->where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('is_primary')
            ->latest('id')
            ->first();

        if (! $assignment) {
            return $defaultRole;
        }

        $tenantRole = TenantRole::query()->find($assignment->tenant_role_id);
        if (! $tenantRole) {
            return $defaultRole;
        }

        $slug = strtolower((string) $tenantRole->slug);
        $name = strtolower((string) $tenantRole->name);
        $identity = trim("{$slug} {$name}");

        if (str_contains($identity, 'teacher') || str_contains($identity, 'lecturer') || str_contains($identity, 'dosen')) {
            return (int) config('moodle.role_map.teacher', 3);
        }

        if (
            str_contains($identity, 'admin') ||
            str_contains($identity, 'manager') ||
            str_contains($identity, 'super')
        ) {
            return (int) config('moodle.role_map.manager', 1);
        }

        return $defaultRole;
    }

    protected function findEntityMapping(string $entityType, int $fosEntityId, string $idnumber): ?MoodleEntityMapping
    {
        return MoodleEntityMapping::query()
            ->where('entity_type', $entityType)
            ->where(function ($query) use ($fosEntityId, $idnumber): void {
                $query->where('fos_entity_id', $fosEntityId)
                    ->orWhere('moodle_idnumber', $idnumber);
            })
            ->first();
    }

    protected function upsertEntityMapping(
        string $entityType,
        int $fosEntityId,
        ?int $tenantId,
        int $moodleId,
        string $moodleIdnumber,
        array $meta = [],
    ): MoodleEntityMapping {
        /** @var MoodleEntityMapping $mapping */
        $mapping = MoodleEntityMapping::query()->updateOrCreate(
            [
                'entity_type' => $entityType,
                'fos_entity_id' => $fosEntityId,
            ],
            [
                'tenant_id' => $tenantId,
                'moodle_id' => $moodleId,
                'moodle_idnumber' => $moodleIdnumber,
                'meta' => $meta,
            ],
        );

        return $mapping;
    }

    protected function callMoodle(string $function, array $params): array
    {
        if (config('moodle.readonly', false)) {
            throw new MoodleReadonlySkipException;
        }

        return $this->client->call($function, $params);
    }
}
