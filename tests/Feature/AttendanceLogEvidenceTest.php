<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Models\Employee;
use Tests\TestCase;

class AttendanceLogEvidenceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_attendance_log_persists_gps_coordinates_and_photo_paths(): void
    {
        [$tenant, $employee] = $this->makeEmployee('attendance-evidence');

        $log = AttendanceLog::query()->create([
            'tenant_id' => $tenant->id,
            'employee_id' => $employee->id,
            'date' => '2026-05-23',
            'status' => 'present',
            'work_hours' => 8,
            'overtime_hours' => 1.5,
            'location_check_in' => [
                'lat' => -6.2,
                'lng' => 106.8,
                'accuracy' => 12,
            ],
            'location_check_out' => [
                'lat' => -6.201,
                'lng' => 106.801,
                'accuracy' => 15,
            ],
            'photo_check_in' => 'attendance/check-in/in.jpg',
            'photo_check_out' => 'attendance/check-out/out.jpg',
            'device_check_in' => 'web',
            'device_check_out' => 'web',
        ]);

        $freshLog = $log->fresh();

        $this->assertSame(-6.2, $freshLog->location_check_in['lat']);
        $this->assertSame(106.801, $freshLog->location_check_out['lng']);
        $this->assertSame('attendance/check-in/in.jpg', $freshLog->photo_check_in);
        $this->assertSame('attendance/check-out/out.jpg', $freshLog->photo_check_out);
        $this->assertEqualsWithDelta(1.5, (float) $freshLog->overtime_hours, 0.01);
    }

    /**
     * @return array{0: Tenant, 1: Employee}
     */
    private function makeEmployee(string $code): array
    {
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => Str::title(str_replace('-', ' ', $code)),
        ]);

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->id,
            'code' => "{$code}-org",
            'name' => "{$code} Organization",
        ]);

        $user = User::factory()->create([
            'email' => "{$code}@example.test",
        ]);

        $employee = Employee::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'employee_number' => Str::upper($code),
            'full_name' => Str::title(str_replace('-', ' ', $code)),
            'email' => "{$code}@example.test",
            'employment_status' => 'active',
            'join_date' => '2026-01-01',
            'basic_salary' => 5_000_000,
        ]);

        return [$tenant, $employee];
    }
}
