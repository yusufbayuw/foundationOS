<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\School\Models\AchievementType;
use Modules\School\Models\Assessment;
use Modules\School\Models\Attendance;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Curriculum;
use Modules\School\Models\Schedule;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\StudentAchievement;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Subject;
use Modules\School\Models\Teacher;
use Tests\Concerns\InteractsWithPdfDocuments;
use Tests\TestCase;

class SchoolDocumentPdfTest extends TestCase
{
    use InteractsWithPdfDocuments;
    use LazilyRefreshDatabase;

    public function test_guest_cannot_download_school_pdfs(): void
    {
        $context = $this->makeSchoolPdfContext();

        $this->getJson(route('school.report-card.pdf', [
            'student' => $context['student']->id,
            'period' => $context['period']->id,
        ]))->assertUnauthorized();

        $this->getJson(route('school.report-card.bulk.pdf', [
            'schoolClass' => $context['class']->id,
            'period' => $context['period']->id,
        ]))->assertUnauthorized();

        $this->getJson(route('school.attendance-recap.pdf', [
            'schoolClass' => $context['class']->id,
            'period' => $context['period']->id,
            'month' => 3,
            'year' => 2026,
        ]))->assertUnauthorized();

        $this->getJson(route('school.student-achievements.pdf', $context['achievement']))
            ->assertUnauthorized();

        $this->getJson(route('school.grade-ledger.pdf', [
            'schoolClass' => $context['class']->id,
            'period' => $context['period']->id,
        ]))->assertUnauthorized();
    }

    public function test_super_admin_can_download_report_card_pdf(): void
    {
        $context = $this->makeSchoolPdfContext();

        $this->actingAs($context['user'])
            ->get(route('school.report-card.pdf', [
                'student' => $context['student']->id,
                'period' => $context['period']->id,
            ]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_bulk_report_card_zip(): void
    {
        $context = $this->makeSchoolPdfContext();

        $this->actingAs($context['user'])
            ->get(route('school.report-card.bulk.pdf', [
                'schoolClass' => $context['class']->id,
                'period' => $context['period']->id,
            ]))
            ->assertOk()
            ->assertHeader('content-type', 'application/zip');
    }

    public function test_super_admin_can_download_attendance_recap_pdf(): void
    {
        $context = $this->makeSchoolPdfContext();

        $this->actingAs($context['user'])
            ->get(route('school.attendance-recap.pdf', [
                'schoolClass' => $context['class']->id,
                'period' => $context['period']->id,
                'month' => 3,
                'year' => 2026,
            ]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_verified_achievement_certificate_pdf(): void
    {
        $context = $this->makeSchoolPdfContext();

        $this->actingAs($context['user'])
            ->get(route('school.student-achievements.pdf', $context['achievement']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_unverified_achievement_certificate_pdf_is_forbidden(): void
    {
        $context = $this->makeSchoolPdfContext();

        $unverified = StudentAchievement::create([
            'tenant_id' => $context['tenant']->id,
            'organization_id' => $context['organization']->id,
            'academic_year_id' => $context['academicYear']->id,
            'student_id' => $context['student']->id,
            'achievement_type_id' => $context['achievementType']->id,
            'title' => 'Draft Achievement',
        ]);

        $this->actingAs($context['user'])
            ->get(route('school.student-achievements.pdf', $unverified))
            ->assertForbidden();
    }

    public function test_super_admin_can_download_class_grade_ledger_pdf(): void
    {
        $context = $this->makeSchoolPdfContext();

        $this->actingAs($context['user'])
            ->get(route('school.grade-ledger.pdf', [
                'schoolClass' => $context['class']->id,
                'period' => $context['period']->id,
            ]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    /**
     * @return array{
     *     tenant: Tenant,
     *     organization: Organization,
     *     user: User,
     *     academicYear: AcademicYear,
     *     period: AcademicPeriod,
     *     class: SchoolClass,
     *     student: Student,
     *     achievement: StudentAchievement,
     *     achievementType: AchievementType,
     * }
     */
    private function makeSchoolPdfContext(): array
    {
        [$tenant, $organization, $user] = $this->makePdfTenantContext();

        $academicYear = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => '2026/2027',
            'code' => 'AY-2026',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Semester Ganjil',
            'code' => '2026-GANJIL',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
        ]);

        $curriculum = Curriculum::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_period_id' => $period->id,
            'name' => 'Kurikulum Merdeka',
            'code' => 'KM-2026',
        ]);

        $subject = Subject::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'curriculum_id' => $curriculum->id,
            'name' => 'Matematika',
            'code' => 'MTK',
        ]);

        $teacher = Teacher::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'nip' => '19880001',
        ]);

        $studentUser = User::factory()->create(['name' => 'Budi PDF']);

        $student = Student::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $studentUser->id,
            'nis' => 'S-PDF-001',
        ]);

        $class = SchoolClass::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_period_id' => $period->id,
            'homeroom_teacher_id' => $teacher->id,
            'name' => 'X-A',
            'code' => 'X-A',
        ]);

        ClassStudent::create([
            'tenant_id' => $tenant->id,
            'academic_period_id' => $period->id,
            'class_id' => $class->id,
            'student_id' => $student->id,
        ]);

        $assessment = Assessment::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_period_id' => $period->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'name' => 'PTS Matematika',
            'type' => 'written',
        ]);

        StudentGrade::create([
            'tenant_id' => $tenant->id,
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'score' => 85,
            'final_score' => 85,
        ]);

        $schedule = Schedule::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_period_id' => $period->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 1,
            'start_time' => '07:00',
            'end_time' => '08:00',
        ]);

        Attendance::create([
            'tenant_id' => $tenant->id,
            'student_id' => $student->id,
            'schedule_id' => $schedule->id,
            'attendance_date' => '2026-03-10',
            'status' => 'present',
        ]);

        $achievementType = AchievementType::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'OLIM',
            'name' => 'Olimpiade',
        ]);

        $achievement = StudentAchievement::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_year_id' => $academicYear->id,
            'student_id' => $student->id,
            'achievement_type_id' => $achievementType->id,
            'title' => 'Juara 1 Matematika',
            'event_name' => 'Olimpiade Kota',
            'event_date' => '2026-02-01',
            'verified_at' => now(),
            'verified_by' => $user->id,
        ]);

        return [
            'tenant' => $tenant,
            'organization' => $organization,
            'user' => $user,
            'academicYear' => $academicYear,
            'period' => $period,
            'class' => $class,
            'student' => $student,
            'achievement' => $achievement,
            'achievementType' => $achievementType,
        ];
    }
}
