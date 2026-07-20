<?php

use App\Http\Controllers\Api\OpenApiController;
use App\Http\Controllers\Api\v1\ApplicantController;
use App\Http\Controllers\Api\v1\Auth\OtpController;
use App\Http\Controllers\Api\v1\Auth\PasswordOtpController;
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

RateLimiter::for('otp:request', function (Request $request) {
    return Limit::perMinute(1)->by(strtolower((string) $request->input('identifier', $request->ip())));
});

RateLimiter::for('otp:verify', function (Request $request) {
    return Limit::perMinute(5)->by(strtolower((string) $request->input('identifier', $request->ip())).'|'.$request->ip());
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
    Route::post('auth/otp/request', [OtpController::class, 'request'])->middleware('throttle:otp:request');
    Route::post('auth/otp/verify', [OtpController::class, 'verify'])->middleware('throttle:otp:verify');
    Route::post('auth/password/forgot', [PasswordOtpController::class, 'forgot'])->middleware('throttle:otp:request');
    Route::post('auth/password/reset-with-otp', [PasswordOtpController::class, 'reset'])->middleware('throttle:otp:verify');

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
