<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CollegeStudentController;
use App\Http\Controllers\Api\v1\CourseController;
use App\Http\Controllers\Api\v1\EmployeeController;
use App\Http\Controllers\Api\v1\OrganizationController;
use App\Http\Controllers\Api\v1\SchoolClassController;
use App\Http\Controllers\Api\v1\StudentController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

RateLimiter::for('api', function (Request $request) {
    $token = $request->user()?->currentAccessToken();
    $key = $token?->id ?? $request->ip();

    return Limit::perMinute(60)->by($key);
});

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
    });
});
