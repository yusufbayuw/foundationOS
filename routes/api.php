<?php

use App\Http\Controllers\Api\OpenApiController;
use App\Http\Controllers\Api\V1\App\DonationController as AppDonationController;
use App\Http\Controllers\Api\V1\App\EventController as AppEventController;
use App\Http\Controllers\Api\V1\App\JobController as AppJobController;
use App\Http\Controllers\Api\V1\App\NotificationController as AppNotificationController;
use App\Http\Controllers\Api\V1\App\ProfileController as AppProfileController;
use App\Http\Controllers\Api\V1\App\ShopController as AppShopController;
use App\Http\Controllers\Api\V1\App\VoucherController as AppVoucherController;
use App\Http\Controllers\Api\v1\ApplicantController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CollegeStudentController;
use App\Http\Controllers\Api\v1\CourseController;
use App\Http\Controllers\Api\v1\DeviceController;
use App\Http\Controllers\Api\v1\EmployeeController;
use App\Http\Controllers\Api\v1\LeaveRequestController;
use App\Http\Controllers\Api\v1\OrganizationController;
use App\Http\Controllers\Api\v1\PaymentController;
use App\Http\Controllers\Api\v1\SchoolClassController;
use App\Http\Controllers\Api\v1\StudentController;
use App\Http\Controllers\Api\v1\StudentDashboardController;
use App\Http\Controllers\Api\v2\VersionController as V2VersionController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\LetterVerificationController;
use Modules\Messaging\Http\Controllers\WhatsAppWebhookController;

RateLimiter::for('api', function (Request $request) {
    $token = $request->user()?->currentAccessToken();
    $key = $token?->id ?? $request->ip();

    return Limit::perMinute(60)->by($key);
});

Route::post('/webhooks/whatsapp/{provider}', [WhatsAppWebhookController::class, 'handle'])
    ->name('webhooks.whatsapp');

Route::get('/openapi.json', [OpenApiController::class, 'v1'])
    ->name('api.openapi');

Route::get('/v2/openapi.json', [OpenApiController::class, 'v2'])
    ->name('api.openapi.v2');

Route::get('/letters/verify/{token}', [LetterVerificationController::class, 'show'])
    ->name('letters.verify');

Route::prefix('v1')->middleware(['throttle:api'])->group(function () {
    Route::middleware(['auth:sanctum', 'resolve.api.tenant'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::get('tenants/current', [AuthController::class, 'currentTenant']);

        // Core resources (read-only)
        Route::get('organizations', [OrganizationController::class, 'index']);
        Route::get('organizations/{id}', [OrganizationController::class, 'show']);

        Route::get('students', [StudentController::class, 'index']);
        Route::get('students/{id}', [StudentController::class, 'show']);

        Route::get('college-students', [CollegeStudentController::class, 'index']);
        Route::get('college-students/{id}', [CollegeStudentController::class, 'show']);

        Route::get('classes', [SchoolClassController::class, 'index']);
        Route::get('classes/{id}', [SchoolClassController::class, 'show']);

        Route::get('courses', [CourseController::class, 'index']);
        Route::get('courses/{id}', [CourseController::class, 'show']);

        Route::get('employees', [EmployeeController::class, 'index']);
        Route::get('employees/{id}', [EmployeeController::class, 'show']);

        // Write endpoints (idempotency key supported)
        Route::middleware(['idempotency'])->group(function () {
            Route::post('applicants', [ApplicantController::class, 'store']);
            Route::post('payments', [PaymentController::class, 'store']);
            Route::post('leave-requests', [LeaveRequestController::class, 'store']);
        });

        // Mobile-first endpoints
        Route::post('devices', [DeviceController::class, 'store']);
        Route::delete('devices/{token}', [DeviceController::class, 'destroy']);
        Route::get('students/{id}/dashboard', [StudentDashboardController::class, 'show']);

        Route::prefix('app')->name('app.')->group(function (): void {
            Route::get('profile', [AppProfileController::class, 'show'])->name('profile.show');
            Route::put('profile', [AppProfileController::class, 'update'])->name('profile.update');
            Route::get('donations', [AppDonationController::class, 'index'])->name('donations.index');
            Route::get('donations/me', [AppDonationController::class, 'mine'])->name('donations.mine');
            Route::get('donations/{campaign}', [AppDonationController::class, 'show'])->name('donations.show');
            Route::post('donations/{campaign}/checkout', [AppDonationController::class, 'checkout'])->name('donations.checkout');
            Route::get('shop/products', [AppShopController::class, 'products'])->name('shop.products.index');
            Route::get('shop/products/{product}', [AppShopController::class, 'product'])->name('shop.products.show');
            Route::post('shop/cart/items', [AppShopController::class, 'addCartItem'])->name('shop.cart.items.store');
            Route::post('shop/checkout', [AppShopController::class, 'checkout'])->name('shop.checkout');
            Route::get('shop/orders/me', [AppShopController::class, 'orders'])->name('shop.orders.mine');
            Route::get('events', [AppEventController::class, 'index'])->name('events.index');
            Route::get('events/{event}', [AppEventController::class, 'show'])->name('events.show');
            Route::get('jobs', [AppJobController::class, 'index'])->name('jobs.index');
            Route::get('jobs/{jobPosting}', [AppJobController::class, 'show'])->name('jobs.show');
            Route::get('vouchers', [AppVoucherController::class, 'index'])->name('vouchers.index');
            Route::post('vouchers/{voucher}/claim', [AppVoucherController::class, 'claim'])->name('vouchers.claim');
            Route::post('vouchers/claims/{claim}/redeem', [AppVoucherController::class, 'redeem'])->name('vouchers.claims.redeem');
            Route::get('notifications', [AppNotificationController::class, 'index'])->name('notifications.index');
        });
    });
});

Route::prefix('v2')->middleware(['throttle:api', 'api.version.meta:v2'])->group(function () {
    Route::get('/', [V2VersionController::class, 'show']);

    Route::middleware(['auth:sanctum', 'resolve.api.tenant'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::get('tenants/current', [AuthController::class, 'currentTenant']);

        Route::get('organizations', [OrganizationController::class, 'index']);
        Route::get('organizations/{id}', [OrganizationController::class, 'show']);

        Route::get('students', [StudentController::class, 'index']);
        Route::get('students/{id}', [StudentController::class, 'show']);

        Route::get('college-students', [CollegeStudentController::class, 'index']);
        Route::get('college-students/{id}', [CollegeStudentController::class, 'show']);

        Route::get('classes', [SchoolClassController::class, 'index']);
        Route::get('classes/{id}', [SchoolClassController::class, 'show']);

        Route::get('courses', [CourseController::class, 'index']);
        Route::get('courses/{id}', [CourseController::class, 'show']);

        Route::get('employees', [EmployeeController::class, 'index']);
        Route::get('employees/{id}', [EmployeeController::class, 'show']);

        Route::middleware(['idempotency'])->group(function () {
            Route::post('applicants', [ApplicantController::class, 'store']);
            Route::post('payments', [PaymentController::class, 'store']);
            Route::post('leave-requests', [LeaveRequestController::class, 'store']);
        });

        Route::post('devices', [DeviceController::class, 'store']);
        Route::delete('devices/{token}', [DeviceController::class, 'destroy']);
        Route::get('students/{id}/dashboard', [StudentDashboardController::class, 'show']);
    });
});
