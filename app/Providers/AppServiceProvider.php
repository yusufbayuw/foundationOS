<?php

namespace App\Providers;

use App\Events\TenantSwitched;
use App\Listeners\LogTenantSwitchAudit;
use App\Models\Permission;
use App\Models\Role;
use App\Observers\ClassStudentObserver;
use App\Observers\CourseObserver;
use App\Observers\CourseOfferingLecturerObserver;
use App\Observers\CourseOfferingObserver;
use App\Observers\StudentObserver;
use App\Observers\StudyPlanItemObserver;
use App\Observers\StudyPlanObserver;
use App\Observers\UserObserver;
use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\CourseOfferingLecturer;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\Applicant;
use Modules\Library\Models\Book;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\VendorBill;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrentTenant::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Course::observe(CourseObserver::class);
        Student::observe(StudentObserver::class);
        ClassStudent::observe(ClassStudentObserver::class);
        CourseOffering::observe(CourseOfferingObserver::class);
        CourseOfferingLecturer::observe(CourseOfferingLecturerObserver::class);
        StudyPlan::observe(StudyPlanObserver::class);
        StudyPlanItem::observe(StudyPlanItemObserver::class);

        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        Gate::before(function ($user, string $ability): ?bool {
            if ($user instanceof User && $user->isGlobalSuperAdmin()) {
                return true;
            }

            return null;
        });

        Relation::enforceMorphMap([
            'user' => User::class,
            'tenant' => Tenant::class,
            'organization' => Organization::class,
            'school_student' => Student::class,
            'enrollment_applicant' => Applicant::class,
            'campus_collage_student' => CollageStudent::class,
            'campus_study_program' => StudyProgram::class,
            'library_book' => Book::class,
            'procurement_vendor' => Vendor::class,
            'purchase_order' => PurchaseOrder::class,
            'goods_receipt' => GoodsReceipt::class,
            'vendor_bill' => VendorBill::class,
        ]);

        Event::listen(TenantSwitched::class, LogTenantSwitchAudit::class);

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
