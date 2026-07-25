<?php

namespace Tests\Feature;

use Modules\Employee\Filament\Resources\AttendanceLogs\Schemas\AttendanceLogForm;
use ReflectionClass;
use Tests\TestCase;

class AttendanceLogFormGeolocationNotificationTest extends TestCase
{
    public function test_geolocation_errors_use_filament_danger_notifications_instead_of_alerts(): void
    {
        $script = $this->geolocationCaptureScript();

        $this->assertStringNotContainsString('alert(', $script);
        $this->assertStringContainsString('new FilamentNotification()', $script);
        $this->assertStringContainsString(".title('Location unavailable')", $script);
        $this->assertStringContainsString('.danger()', $script);
        $this->assertStringContainsString(".body(message)", $script);
        $this->assertStringContainsString("this.notifyGeolocationError('Geolocation is not supported by this browser.')", $script);
        $this->assertStringContainsString('err => this.notifyGeolocationError(`Location error: ${err.message}`)', $script);
    }

    private function geolocationCaptureScript(): string
    {
        $reflection = new ReflectionClass(AttendanceLogForm::class);
        $method = $reflection->getMethod('geolocationCaptureScript');

        return $method->invoke(null);
    }
}
