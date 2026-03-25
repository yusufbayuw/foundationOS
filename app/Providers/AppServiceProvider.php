<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Modules\Campus\Models\CollageStudent;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\Applicant;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\VendorBill;
use Modules\School\Models\Student;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        Model::unguard();

        Relation::enforceMorphMap([
            'user' => User::class,
            'tenant' => Tenant::class,
            'organization' => Organization::class,
            'school_student' => Student::class,
            'enrollment_applicant' => Applicant::class,
            'campus_collage_student' => CollageStudent::class,
            'procurement_vendor' => Vendor::class,
            'purchase_order' => PurchaseOrder::class,
            'goods_receipt' => GoodsReceipt::class,
            'vendor_bill' => VendorBill::class,
        ]);

        if (config("app.env") === "production") {
            URL::forceScheme('https');
        }
    }
}
