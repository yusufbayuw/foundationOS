<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Global\Models\City;
use Modules\Global\Models\Country;
use Modules\Global\Models\District;
use Modules\Global\Models\Province;
use Modules\Global\Models\Village;
use Modules\School\Models\Assessment;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Curriculum;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\Subject;
use Modules\School\Models\Teacher;
use Tests\TestCase;

class GlobalAndSchoolFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_location_hierarchy_is_connected(): void
    {
        $country = Country::create([
            'code' => 'ID',
            'name' => 'Indonesia',
        ]);

        $province = Province::create([
            'country_id' => $country->id,
            'code' => 'JK',
            'name' => 'DKI Jakarta',
        ]);

        $city = City::create([
            'province_id' => $province->id,
            'code' => 'JKTSEL',
            'name' => 'Jakarta Selatan',
        ]);

        $district = District::create([
            'city_id' => $city->id,
            'code' => 'KEBAYORAN',
            'name' => 'Kebayoran Baru',
        ]);

        $village = Village::create([
            'district_id' => $district->id,
            'code' => 'GUNAWAN',
            'name' => 'Gunawarman',
        ]);

        $this->assertSame($country->id, $province->country->id);
        $this->assertSame($province->id, $city->province->id);
        $this->assertSame($city->id, $district->city->id);
        $this->assertSame($district->id, $village->district->id);
    }

    public function test_school_entities_are_tenant_scoped_and_related(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'school-pro',
            'name' => 'School Pro',
            'included_modules' => ['core', 'school'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'smk-utama',
            'name' => 'SMK Utama',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'campus-a',
            'name' => 'Campus A',
        ]);

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

        $student = Student::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'nis' => 'S-001',
        ]);

        $class = SchoolClass::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_period_id' => $period->id,
            'homeroom_teacher_id' => $teacher->id,
            'name' => 'X-A',
            'code' => 'X-A',
        ]);

        $classStudent = ClassStudent::create([
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
        ]);

        $this->assertSame($tenant->id, $curriculum->tenant->id);
        $this->assertSame($curriculum->id, $subject->curriculum->id);
        $this->assertSame($teacher->id, $class->homeroomTeacher->id);
        $this->assertSame($class->id, $classStudent->schoolClass->id);
        $this->assertSame($subject->id, $assessment->subject->id);
    }
}
