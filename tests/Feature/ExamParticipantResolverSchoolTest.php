<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamToken;
use Modules\Exam\Services\ExamParticipantResolver;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\Subject;
use Tests\TestCase;

class ExamParticipantResolverSchoolTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolve_from_school_class_creates_participants_with_tokens(): void
    {
        [$tenant, $class, $exam] = $this->seedSchoolExam();

        $user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi'.Str::random(4).'@test.com',
            'password' => 'password',
        ]);

        $student = Student::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'nis' => '12345',
        ]);

        ClassStudent::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'active',
        ]);

        $result = app(ExamParticipantResolver::class)->resolveFromSchoolClass($exam);

        $this->assertSame(1, $result['created']);
        $this->assertSame(0, $result['skipped']);

        $participant = ExamParticipant::withoutTenantScope()->first();

        $this->assertNotNull($participant);
        $this->assertTrue(Str::isUuid($participant->id));
        $this->assertSame('Budi Santoso', $participant->student_name);
        $this->assertSame('12345', $participant->student_identifier);
        $this->assertSame(ParticipantSource::SchoolStudent, $participant->participant_source);
        $this->assertSame(ParticipantStatus::Assigned, $participant->status);
        $this->assertSame($student->id, $participant->school_student_reference);
        $this->assertSame($student->id, $participant->participant_legacy_id);
        $this->assertNotSame($student->id, $participant->getKey());

        $token = ExamToken::withoutTenantScope()->where('exam_participant_id', $participant->id)->first();
        $this->assertNotNull($token);
        $this->assertTrue($token->is_active);
        $this->assertNotEmpty($token->token);

        $again = app(ExamParticipantResolver::class)->resolveFromSchoolClass($exam);
        $this->assertSame(0, $again['created']);
        $this->assertSame(1, $again['skipped']);
    }

    /**
     * @return array{0: Tenant, 1: SchoolClass, 2: ExamDefinition}
     */
    protected function seedSchoolExam(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-b5-school',
            'name' => 'Exam B5 School',
            'included_modules' => ['core', 'school', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'b5-school',
            'name' => 'B5 School Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main',
        ]);

        $year = AcademicYear::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => '2025/2026',
            'code' => '2526',
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
            'name' => 'Math',
            'code' => 'MAT',
        ]);

        $class = SchoolClass::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_period_id' => $period->id,
            'name' => 'X-A',
            'code' => 'X-A',
        ]);

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'UTS Math',
            'exam_academic_context' => ExamAcademicContext::School,
            'status' => ExamStatus::Draft,
            'school_class_reference' => $class->id,
            'school_subject_reference' => $subject->id,
        ]);

        return [$tenant, $class, $exam];
    }
}
