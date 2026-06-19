# API Routes Catalog

Complete application API route list (`php artisan route:list` bootstrap, URI prefix `api/`).

**Generated:** 2026-06-19T04:50:20+00:00  
**Source:** `scripts/extract-api-routes.php` → `docs/catalogs/api-routes-catalog.json`  
**Route count:** 101

---

## Summary by module

| Module | Routes |
|--------|--------|
| Api | 43 |
| Campus | 5 |
| Core | 5 |
| EOffice | 1 |
| Employee | 4 |
| Enrollment | 1 |
| Exam | 1 |
| Finance | 5 |
| Global | 5 |
| Inventory | 5 |
| Library | 5 |
| Messaging | 1 |
| Monitoring | 5 |
| Procurement | 5 |
| Sales | 5 |
| School | 5 |

---

## Middleware patterns

| Pattern | Routes | Meaning |
|---------|--------|---------|
| `auth:sanctum` + `resolve.api.tenant` | Core mobile/integrator v1/v2 | Bearer token with tenant context |
| `auth:sanctum` only | `api/exam/runtime/*` | Exam runtime (no `resolve.api.tenant` on route group) |
| `throttle:api` | Most v1/v2 groups | 60 req/min per token or IP (`routes/api.php`) |
| `idempotency` | POST applicants, payments, leave-requests | Duplicate-safe writes |
| Public (no auth) | `openapi.json`, `letters/verify`, `api/inquiry` | See table below |

---

## Full route table

| Method | URI | Route name | Module | Middleware | Action |
|--------|-----|------------|--------|------------|--------|
| POST | `api/exam/runtime/attempts` | api.exam.runtime.attempts.store | Exam | api, auth:sanctum | `Modules\Exam\Http\Controllers\Api\ExamRuntimeWebhookController@storeAttempt` |
| POST | `api/inquiry` | api.enrollment.inquiry | Enrollment | api, throttle:10,1 | `Modules\Enrollment\Http\Controllers\InquiryController@store` |
| GET | `api/letters/verify/{token}` | letters.verify | EOffice | api | `Modules\EOffice\Http\Controllers\LetterVerificationController@show` |
| GET | `api/openapi.json` | api.openapi | Api | api | `App\Http\Controllers\Api\OpenApiController@v1` |
| POST | `api/v1/applicants` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant, idempotency | `App\Http\Controllers\Api\v1\ApplicantController@store` |
| DELETE | `api/v1/campuses/{campus}` | api.campus.destroy | Campus | api, auth:sanctum | `Modules\Campus\Http\Controllers\CampusController@destroy` |
| GET | `api/v1/campuses/{campus}` | api.campus.show | Campus | api, auth:sanctum | `Modules\Campus\Http\Controllers\CampusController@show` |
| PUT|PATCH | `api/v1/campuses/{campus}` | api.campus.update | Campus | api, auth:sanctum | `Modules\Campus\Http\Controllers\CampusController@update` |
| GET | `api/v1/campuses` | api.campus.index | Campus | api, auth:sanctum | `Modules\Campus\Http\Controllers\CampusController@index` |
| POST | `api/v1/campuses` | api.campus.store | Campus | api, auth:sanctum | `Modules\Campus\Http\Controllers\CampusController@store` |
| GET | `api/v1/classes/{id}` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\SchoolClassController@show` |
| GET | `api/v1/classes` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\SchoolClassController@index` |
| GET | `api/v1/college-students/{id}` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\CollegeStudentController@show` |
| GET | `api/v1/college-students` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\CollegeStudentController@index` |
| DELETE | `api/v1/cores/{core}` | api.core.destroy | Core | api, auth:sanctum | `Modules\Core\Http\Controllers\CoreController@destroy` |
| GET | `api/v1/cores/{core}` | api.core.show | Core | api, auth:sanctum | `Modules\Core\Http\Controllers\CoreController@show` |
| PUT|PATCH | `api/v1/cores/{core}` | api.core.update | Core | api, auth:sanctum | `Modules\Core\Http\Controllers\CoreController@update` |
| GET | `api/v1/cores` | api.core.index | Core | api, auth:sanctum | `Modules\Core\Http\Controllers\CoreController@index` |
| POST | `api/v1/cores` | api.core.store | Core | api, auth:sanctum | `Modules\Core\Http\Controllers\CoreController@store` |
| GET | `api/v1/courses/{id}` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\CourseController@show` |
| GET | `api/v1/courses` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\CourseController@index` |
| DELETE | `api/v1/devices/{token}` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\DeviceController@destroy` |
| POST | `api/v1/devices` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\DeviceController@store` |
| DELETE | `api/v1/employees/{employee}` | api.employee.destroy | Employee | api, auth:sanctum | `Modules\Employee\Http\Controllers\EmployeeController@destroy` |
| GET | `api/v1/employees/{employee}` | api.employee.show | Employee | api, auth:sanctum | `Modules\Employee\Http\Controllers\EmployeeController@show` |
| PUT|PATCH | `api/v1/employees/{employee}` | api.employee.update | Employee | api, auth:sanctum | `Modules\Employee\Http\Controllers\EmployeeController@update` |
| GET | `api/v1/employees/{id}` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\EmployeeController@show` |
| GET | `api/v1/employees` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\EmployeeController@index` |
| POST | `api/v1/employees` | api.employee.store | Employee | api, auth:sanctum | `Modules\Employee\Http\Controllers\EmployeeController@store` |
| DELETE | `api/v1/finances/{finance}` | api.finance.destroy | Finance | api, auth:sanctum | `Modules\Finance\Http\Controllers\FinanceController@destroy` |
| GET | `api/v1/finances/{finance}` | api.finance.show | Finance | api, auth:sanctum | `Modules\Finance\Http\Controllers\FinanceController@show` |
| PUT|PATCH | `api/v1/finances/{finance}` | api.finance.update | Finance | api, auth:sanctum | `Modules\Finance\Http\Controllers\FinanceController@update` |
| GET | `api/v1/finances` | api.finance.index | Finance | api, auth:sanctum | `Modules\Finance\Http\Controllers\FinanceController@index` |
| POST | `api/v1/finances` | api.finance.store | Finance | api, auth:sanctum | `Modules\Finance\Http\Controllers\FinanceController@store` |
| DELETE | `api/v1/globals/{global}` | api.global.destroy | Global | api, auth:sanctum | `Modules\Global\Http\Controllers\GlobalController@destroy` |
| GET | `api/v1/globals/{global}` | api.global.show | Global | api, auth:sanctum | `Modules\Global\Http\Controllers\GlobalController@show` |
| PUT|PATCH | `api/v1/globals/{global}` | api.global.update | Global | api, auth:sanctum | `Modules\Global\Http\Controllers\GlobalController@update` |
| GET | `api/v1/globals` | api.global.index | Global | api, auth:sanctum | `Modules\Global\Http\Controllers\GlobalController@index` |
| POST | `api/v1/globals` | api.global.store | Global | api, auth:sanctum | `Modules\Global\Http\Controllers\GlobalController@store` |
| DELETE | `api/v1/inventories/{inventory}` | api.inventory.destroy | Inventory | api, auth:sanctum | `Modules\Inventory\Http\Controllers\InventoryController@destroy` |
| GET | `api/v1/inventories/{inventory}` | api.inventory.show | Inventory | api, auth:sanctum | `Modules\Inventory\Http\Controllers\InventoryController@show` |
| PUT|PATCH | `api/v1/inventories/{inventory}` | api.inventory.update | Inventory | api, auth:sanctum | `Modules\Inventory\Http\Controllers\InventoryController@update` |
| GET | `api/v1/inventories` | api.inventory.index | Inventory | api, auth:sanctum | `Modules\Inventory\Http\Controllers\InventoryController@index` |
| POST | `api/v1/inventories` | api.inventory.store | Inventory | api, auth:sanctum | `Modules\Inventory\Http\Controllers\InventoryController@store` |
| POST | `api/v1/leave-requests` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant, idempotency | `App\Http\Controllers\Api\v1\LeaveRequestController@store` |
| DELETE | `api/v1/libraries/{library}` | api.library.destroy | Library | api, auth:sanctum | `Modules\Library\Http\Controllers\LibraryController@destroy` |
| GET | `api/v1/libraries/{library}` | api.library.show | Library | api, auth:sanctum | `Modules\Library\Http\Controllers\LibraryController@show` |
| PUT|PATCH | `api/v1/libraries/{library}` | api.library.update | Library | api, auth:sanctum | `Modules\Library\Http\Controllers\LibraryController@update` |
| GET | `api/v1/libraries` | api.library.index | Library | api, auth:sanctum | `Modules\Library\Http\Controllers\LibraryController@index` |
| POST | `api/v1/libraries` | api.library.store | Library | api, auth:sanctum | `Modules\Library\Http\Controllers\LibraryController@store` |
| GET | `api/v1/me` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\AuthController@me` |
| DELETE | `api/v1/monitorings/{monitoring}` | api.monitoring.destroy | Monitoring | api, auth:sanctum | `Modules\Monitoring\Http\Controllers\MonitoringController@destroy` |
| GET | `api/v1/monitorings/{monitoring}` | api.monitoring.show | Monitoring | api, auth:sanctum | `Modules\Monitoring\Http\Controllers\MonitoringController@show` |
| PUT|PATCH | `api/v1/monitorings/{monitoring}` | api.monitoring.update | Monitoring | api, auth:sanctum | `Modules\Monitoring\Http\Controllers\MonitoringController@update` |
| GET | `api/v1/monitorings` | api.monitoring.index | Monitoring | api, auth:sanctum | `Modules\Monitoring\Http\Controllers\MonitoringController@index` |
| POST | `api/v1/monitorings` | api.monitoring.store | Monitoring | api, auth:sanctum | `Modules\Monitoring\Http\Controllers\MonitoringController@store` |
| GET | `api/v1/organizations/{id}` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\OrganizationController@show` |
| GET | `api/v1/organizations` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\OrganizationController@index` |
| POST | `api/v1/payments` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant, idempotency | `App\Http\Controllers\Api\v1\PaymentController@store` |
| DELETE | `api/v1/procurements/{procurement}` | api.procurement.destroy | Procurement | api, auth:sanctum | `Modules\Procurement\Http\Controllers\ProcurementController@destroy` |
| GET | `api/v1/procurements/{procurement}` | api.procurement.show | Procurement | api, auth:sanctum | `Modules\Procurement\Http\Controllers\ProcurementController@show` |
| PUT|PATCH | `api/v1/procurements/{procurement}` | api.procurement.update | Procurement | api, auth:sanctum | `Modules\Procurement\Http\Controllers\ProcurementController@update` |
| GET | `api/v1/procurements` | api.procurement.index | Procurement | api, auth:sanctum | `Modules\Procurement\Http\Controllers\ProcurementController@index` |
| POST | `api/v1/procurements` | api.procurement.store | Procurement | api, auth:sanctum | `Modules\Procurement\Http\Controllers\ProcurementController@store` |
| DELETE | `api/v1/sales/{sale}` | api.sales.destroy | Sales | api, auth:sanctum | `Modules\Sales\Http\Controllers\SalesController@destroy` |
| GET | `api/v1/sales/{sale}` | api.sales.show | Sales | api, auth:sanctum | `Modules\Sales\Http\Controllers\SalesController@show` |
| PUT|PATCH | `api/v1/sales/{sale}` | api.sales.update | Sales | api, auth:sanctum | `Modules\Sales\Http\Controllers\SalesController@update` |
| GET | `api/v1/sales` | api.sales.index | Sales | api, auth:sanctum | `Modules\Sales\Http\Controllers\SalesController@index` |
| POST | `api/v1/sales` | api.sales.store | Sales | api, auth:sanctum | `Modules\Sales\Http\Controllers\SalesController@store` |
| DELETE | `api/v1/schools/{school}` | api.school.destroy | School | api, auth:sanctum | `Modules\School\Http\Controllers\SchoolController@destroy` |
| GET | `api/v1/schools/{school}` | api.school.show | School | api, auth:sanctum | `Modules\School\Http\Controllers\SchoolController@show` |
| PUT|PATCH | `api/v1/schools/{school}` | api.school.update | School | api, auth:sanctum | `Modules\School\Http\Controllers\SchoolController@update` |
| GET | `api/v1/schools` | api.school.index | School | api, auth:sanctum | `Modules\School\Http\Controllers\SchoolController@index` |
| POST | `api/v1/schools` | api.school.store | School | api, auth:sanctum | `Modules\School\Http\Controllers\SchoolController@store` |
| GET | `api/v1/students/{id}/dashboard` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\StudentDashboardController@show` |
| GET | `api/v1/students/{id}` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\StudentController@show` |
| GET | `api/v1/students` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\StudentController@index` |
| GET | `api/v1/tenants/current` | — | Api | api, throttle:api, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\AuthController@currentTenant` |
| POST | `api/v2/applicants` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant, idempotency | `App\Http\Controllers\Api\v1\ApplicantController@store` |
| GET | `api/v2/classes/{id}` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\SchoolClassController@show` |
| GET | `api/v2/classes` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\SchoolClassController@index` |
| GET | `api/v2/college-students/{id}` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\CollegeStudentController@show` |
| GET | `api/v2/college-students` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\CollegeStudentController@index` |
| GET | `api/v2/courses/{id}` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\CourseController@show` |
| GET | `api/v2/courses` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\CourseController@index` |
| DELETE | `api/v2/devices/{token}` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\DeviceController@destroy` |
| POST | `api/v2/devices` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\DeviceController@store` |
| GET | `api/v2/employees/{id}` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\EmployeeController@show` |
| GET | `api/v2/employees` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\EmployeeController@index` |
| POST | `api/v2/leave-requests` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant, idempotency | `App\Http\Controllers\Api\v1\LeaveRequestController@store` |
| GET | `api/v2/me` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\AuthController@me` |
| GET | `api/v2/openapi.json` | api.openapi.v2 | Api | api | `App\Http\Controllers\Api\OpenApiController@v2` |
| GET | `api/v2/organizations/{id}` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\OrganizationController@show` |
| GET | `api/v2/organizations` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\OrganizationController@index` |
| POST | `api/v2/payments` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant, idempotency | `App\Http\Controllers\Api\v1\PaymentController@store` |
| GET | `api/v2/students/{id}/dashboard` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\StudentDashboardController@show` |
| GET | `api/v2/students/{id}` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\StudentController@show` |
| GET | `api/v2/students` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\StudentController@index` |
| GET | `api/v2/tenants/current` | — | Api | api, throttle:api, api.version.meta:v2, auth:sanctum, resolve.api.tenant | `App\Http\Controllers\Api\v1\AuthController@currentTenant` |
| GET | `api/v2` | — | Api | api, throttle:api, api.version.meta:v2 | `App\Http\Controllers\Api\v2\VersionController@show` |
| POST | `api/webhooks/whatsapp/{provider}` | webhooks.whatsapp | Messaging | api | `Modules\Messaging\Http\Controllers\WhatsAppWebhookController@handle` |

---

## Regenerate

```bash
php scripts/extract-api-routes.php
php scripts/generate-docs-appendices.php
```
