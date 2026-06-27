<?php

namespace Tests\ImportExport;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyResult;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\GradebookExportStatus;
use Modules\Exam\Enums\GradebookExportTargetModule;
use Modules\Exam\Events\ExamResultPublished;
use Modules\Exam\Events\ExamResultSynced;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamGradebookExportLog;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Services\ExamGradebookEventDispatcher;
use Modules\Exam\Services\ExamGradebookExportService;
use Modules\Exam\Services\SchoolGradeBridgeService;
use Modules\School\Models\Assessment;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Subject;
use Tests\TestCase;

class ExamGradebookExportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_school_gradebook_export_creates_student_grade_and_uuid_log(): void
    {
        [$tenant, $definition, $participant, $result] = $this->seedSchoolExamWithResult(score: 88);

        $summary = app(ExamGradebookExportService::class)->pushToSchoolGradebook($definition);

        $this->assertSame(1, $summary['success']);
        $this->assertDatabaseHas('student_grades', [
            'student_id' => $participant->school_student_reference,
            'assessment_id' => $definition->school_assessment_id,
            'score' => 88,
        ]);

        $log = ExamGradebookExportLog::query()->first();
        $this->assertNotNull($log);
        $this->assertTrue(Str::isUuid((string) $log->id));
        $this->assertSame(GradebookExportTargetModule::School, $log->target_module);
        $this->assertSame(GradebookExportStatus::Success, $log->status);
        $this->assertSame(StudentGrade::class, $log->target_reference_type);
        $this->assertNotNull($log->target_reference_id);
    }

    public function test_campus_gradebook_export_updates_study_result_component(): void
    {
        [$tenant, $definition, $participant, $result, $planItem] = $this->seedCampusExamWithResult(score: 76);

        $summary = app(ExamGradebookExportService::class)->pushToCampusGradebook($definition);

        $this->assertSame(1, $summary['success']);

        $studyResult = StudyResult::query()
            ->where('study_plan_item_id', $planItem->id)
            ->first();

        $this->assertNotNull($studyResult);
        $this->assertSame(76.0, (float) ($studyResult->components_breakdown['final'] ?? 0));
        $this->assertSame('exam_module', $studyResult->source);

        $log = ExamGradebookExportLog::query()->first();
        $this->assertTrue(Str::isUuid((string) $log?->id));
        $this->assertSame(StudyResult::class, $log?->target_reference_type);
    }

    public function test_skipped_school_export_dispatches_exam_result_synced_event(): void
    {
        Event::fake([ExamResultSynced::class]);

        [, $definition, , $result] = $this->seedSchoolExamWithResult(score: 70, withAssessment: false);

        $summary = app(ExamGradebookExportService::class)->pushToSchoolGradebook($definition);

        $this->assertSame(0, $summary['success']);
        $this->assertSame(1, $summary['skipped']);
        $this->assertSame(1, $summary['events_dispatched']);

        Event::assertDispatched(ExamResultSynced::class, function (ExamResultSynced $event) use ($definition, $result): bool {
            return $event->examId === (string) $definition->id
                && $event->resultId === (string) $result->id
                && $event->participantId === (string) $result->exam_participant_id
                && Str::isUuid($event->resultId);
        });
    }

    public function test_exam_result_synced_event_carries_uuid_payload(): void
    {
        Event::fake([ExamResultSynced::class]);

        [, $definition, , $result] = $this->seedSchoolExamWithResult(score: 55);

        app(ExamGradebookEventDispatcher::class)->synced($result);

        Event::assertDispatched(ExamResultSynced::class, function (ExamResultSynced $event): bool {
            return Str::isUuid($event->examId)
                && Str::isUuid($event->resultId)
                && Str::isUuid($event->participantId);
        });
    }

    public function test_exam_result_published_event_uses_exam_uuid(): void
    {
        Event::fake([ExamResultPublished::class]);

        [, $definition] = $this->seedSchoolExamWithResult(score: 60);

        app(ExamGradebookEventDispatcher::class)->published($definition);

        Event::assertDispatched(ExamResultPublished::class, function (ExamResultPublished $event) use ($definition): bool {
            return $event->examId === (string) $definition->id
                && Str::isUuid($event->examId)
                && $event->resultId === null;
        });
    }

    public function test_school_bridge_export_result_matches_gradebook_service(): void
    {
        [, $definition, , $result] = $this->seedSchoolExamWithResult(score: 91);

        $outcome = app(SchoolGradeBridgeService::class)->exportResult($definition, $result);

        $this->assertSame(GradebookExportStatus::Success, $outcome->status);
        $this->assertSame(StudentGrade::class, $outcome->targetReferenceType);
    }

    /**
     * @return array{0: Tenant, 1: ExamDefinition, 2: ExamParticipant, 3: ExamResult}
     */
    protected function seedSchoolExamWithResult(float $score, bool $withAssessment = true): array
    {
        $tenant = $this->createTenant(['school', 'exam']);

        $year = AcademicYear::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => '2025/2026',
            'code' => '2025',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
        ]);

        $period = AcademicPeriod::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester 1',
            'code' => 'S1',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
        ]);

        $subject = Subject::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Science',
            'code' => 'SCI',
        ]);

        $class = SchoolClass::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_period_id' => $period->id,
            'name' => 'X-B',
            'code' => 'X-B',
        ]);

        $assessmentId = null;

        if ($withAssessment) {
            $assessmentId = Assessment::withoutTenantScope()->create([
                'tenant_id' => $tenant->id,
                'academic_period_id' => $period->id,
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'name' => 'Final Exam',
                'code' => 'FIN-SCI',
            ])->id;
        }

        $student = Student::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'nis' => '88001',
            'nisn' => '8800100001',
        ]);

        $definition = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'School Cloud Exam',
            'exam_academic_context' => ExamAcademicContext::School,
            'school_assessment_id' => $assessmentId,
            'passing_score' => 70,
            'max_score' => 100,
            'status' => ExamStatus::Published,
            'grade_sync_target' => 'final',
        ]);

        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $definition->id,
            'student_name' => 'School Student',
            'school_student_reference' => $student->id,
            'context_reference_type' => Student::class,
            'participant_legacy_id' => $student->id,
        ]);

        $result = ExamResult::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $definition->id,
            'exam_participant_id' => $participant->id,
            'runtime_result_id' => (string) Str::uuid(),
            'score' => $score,
            'max_score' => 100,
            'percentage' => $score,
            'status' => 'final',
        ]);

        return [$tenant, $definition->refresh(), $participant, $result];
    }

    /**
     * @return array{0: Tenant, 1: ExamDefinition, 2: ExamParticipant, 3: ExamResult, 4: StudyPlanItem}
     */
    protected function seedCampusExamWithResult(float $score): array
    {
        $tenant = $this->createTenant(['campus', 'exam']);

        $year = AcademicYear::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => '2025/2026',
            'code' => '2025',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
        ]);

        $period = AcademicPeriod::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester 1',
            'code' => 'S1',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
        ]);

        $course = Course::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Algorithms',
            'code' => 'ALG101',
        ]);

        $offering = CourseOffering::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'course_id' => $course->id,
            'academic_period_id' => $period->id,
            'class_code' => 'ALG-A',
        ]);

        $campusStudent = CollageStudent::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'student_number' => '20250001',
            'full_name' => 'Campus Student',
        ]);

        $studyPlan = StudyPlan::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'collage_student_id' => $campusStudent->id,
            'academic_period_id' => $period->id,
            'plan_number' => 'SP-GB-001',
            'status' => 'approved',
        ]);

        $planItem = StudyPlanItem::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'study_plan_id' => $studyPlan->id,
            'course_offering_id' => $offering->id,
            'course_id' => $course->id,
            'credits' => 3,
            'status' => 'enrolled',
        ]);

        $definition = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Campus Midterm',
            'exam_academic_context' => ExamAcademicContext::Campus,
            'campus_course_reference' => $course->id,
            'campus_class_reference' => $offering->id,
            'passing_score' => 55,
            'max_score' => 100,
            'status' => ExamStatus::Published,
            'grade_sync_target' => 'final',
        ]);

        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $definition->id,
            'student_name' => 'Campus Student',
            'campus_student_reference' => $campusStudent->id,
            'participant_legacy_id' => $campusStudent->id,
            'context_reference_type' => CollageStudent::class,
        ]);

        $result = ExamResult::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $definition->id,
            'exam_participant_id' => $participant->id,
            'runtime_result_id' => (string) Str::uuid(),
            'score' => $score,
            'max_score' => 100,
            'percentage' => $score,
            'status' => 'final',
        ]);

        return [$tenant, $definition->refresh(), $participant, $result, $planItem];
    }

    /**
     * @param  list<string>  $modules
     */
    protected function createTenant(array $modules = ['exam']): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'gradebook-'.Str::random(4),
            'name' => 'Gradebook Plan',
            'included_modules' => array_merge(['core'], $modules),
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'gb-'.Str::random(4),
            'name' => 'Gradebook Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
