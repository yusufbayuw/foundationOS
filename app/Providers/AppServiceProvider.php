<?php

namespace App\Providers;

use App\Events\TenantSwitched;
use App\Listeners\LogTenantSwitchAudit;
use App\Models\Permission;
use App\Models\PersonalAccessToken;
use App\Models\Role;
use App\Models\Voucher;
use App\Models\VoucherClaim;
use App\Observers\AcademicPeriodObserver;
use App\Observers\ClassStudentObserver;
use App\Observers\CourseObserver;
use App\Observers\CourseOfferingLecturerObserver;
use App\Observers\CourseOfferingObserver;
use App\Observers\StudentObserver;
use App\Observers\StudyPlanItemObserver;
use App\Observers\StudyPlanObserver;
use App\Observers\UserObserver;
use App\Payments\NullPaymentGateway;
use App\Payments\PaymentGateway;
use App\Policies\VoucherClaimPolicy;
use App\Policies\VoucherPolicy;
use App\Services\Auth\LogOtpMessenger;
use App\Services\Auth\OtpMessenger;
use App\Support\CurrentTenant;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Modules\Alumni\Models\JobPosting;
use Modules\Alumni\Policies\JobPostingPolicy;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\CourseOfferingLecturer;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Donation\Models\Campaign;
use Modules\Donation\Models\Donation;
use Modules\Donation\Policies\CampaignPolicy;
use Modules\Donation\Policies\DonationPolicy;
use Modules\Enrollment\Models\Applicant;
use Modules\Event\Models\Event as AppEvent;
use Modules\Event\Policies\EventPolicy;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\CustomerInvoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Models\StockAdjustmentLine;
use Modules\Inventory\Models\StockItem;
use Modules\Inventory\Models\StockMove;
use Modules\Inventory\Models\Warehouse;
use Modules\Library\Models\Book;
use Modules\Marketplace\Models\MarketplaceOrder;
use Modules\Marketplace\Models\MarketplaceProduct;
use Modules\Marketplace\Policies\MarketplaceProductPolicy;
use Modules\MerchOrder\Models\MerchOrder;
use Modules\MerchOrder\Policies\MerchOrderPolicy;
use Modules\Monitoring\Listeners\LogSecurityAuthEvents;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\GoodsReceiptItem;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\VendorBill;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;
use Modules\School\Observers\StudentGradeObserver;
use Modules\Workflow\Models\WorkflowInstance;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OtpMessenger::class, LogOtpMessenger::class);
        $this->app->singleton(CurrentTenant::class);
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        $this->app->bind(PaymentGateway::class, NullPaymentGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            $token = $request->user()?->currentAccessToken();
            $key = $token?->id ?? $request->ip();

            return Limit::perMinute(60)->by($key);
        });

        RateLimiter::for('webhooks', fn (Request $request): Limit => Limit::perMinute(30)->by($request->ip()));

        User::observe(UserObserver::class);
        AcademicPeriod::observe(AcademicPeriodObserver::class);
        Course::observe(CourseObserver::class);
        Student::observe(StudentObserver::class);
        ClassStudent::observe(ClassStudentObserver::class);
        CourseOffering::observe(CourseOfferingObserver::class);
        CourseOfferingLecturer::observe(CourseOfferingLecturerObserver::class);
        StudyPlan::observe(StudyPlanObserver::class);
        StudyPlanItem::observe(StudyPlanItemObserver::class);
        StudentGrade::observe(StudentGradeObserver::class);

        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        Gate::before(function ($user, string $ability): ?bool {
            if ($user instanceof User && $user->isGlobalSuperAdmin()) {
                return true;
            }

            return null;
        });

        Gate::define('viewPulse', fn (?User $user = null): bool => $user instanceof User && $user->isGlobalSuperAdmin());

        Gate::policy(Campaign::class, CampaignPolicy::class);
        Gate::policy(Donation::class, DonationPolicy::class);
        Gate::policy(MarketplaceProduct::class, MarketplaceProductPolicy::class);
        Gate::policy(MerchOrder::class, MerchOrderPolicy::class);
        Gate::policy(AppEvent::class, EventPolicy::class);
        Gate::policy(JobPosting::class, JobPostingPolicy::class);
        Gate::policy(Voucher::class, VoucherPolicy::class);
        Gate::policy(VoucherClaim::class, VoucherClaimPolicy::class);

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
            'goods_receipt_item' => GoodsReceiptItem::class,
            'stock_adjustment_line' => StockAdjustmentLine::class,
            'vendor_bill' => VendorBill::class,
            'student_invoice' => StudentInvoice::class,
            'payment' => Payment::class,
            'journal_entry' => JournalEntry::class,
            'budget' => Budget::class,
            'customer_invoice' => CustomerInvoice::class,
            'marketplace_order' => MarketplaceOrder::class,
            'merch_order' => MerchOrder::class,
            'warehouse' => Warehouse::class,
            'stock_item' => StockItem::class,
            'stock_move' => StockMove::class,
            'stock_adjustment' => StockAdjustment::class,
            'workflow_instance' => WorkflowInstance::class,
        ]);

        Event::listen(TenantSwitched::class, LogTenantSwitchAudit::class);
        Event::listen(Failed::class, [LogSecurityAuthEvents::class, 'handleFailed']);
        Event::listen(Lockout::class, [LogSecurityAuthEvents::class, 'handleLockout']);
        Event::listen(Login::class, [LogSecurityAuthEvents::class, 'handleLogin']);

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
