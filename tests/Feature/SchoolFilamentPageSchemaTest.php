<?php

namespace Tests\Feature;

use Filament\Schemas\Schema;
use Modules\School\Filament\Pages\AttendanceRecapPage;
use Modules\School\Filament\Pages\ReportCardPage;
use ReflectionMethod;
use Tests\TestCase;

class SchoolFilamentPageSchemaTest extends TestCase
{
    public function test_school_custom_pages_use_filament_schema_contracts(): void
    {
        foreach ([AttendanceRecapPage::class, ReportCardPage::class] as $pageClass) {
            $method = new ReflectionMethod($pageClass, 'form');
            $parameter = $method->getParameters()[0];

            $this->assertSame(Schema::class, $parameter->getType()?->getName());
            $this->assertSame(Schema::class, $method->getReturnType()?->getName());
        }
    }
}
