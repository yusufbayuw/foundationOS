<?php

namespace Tests\Feature;

use Filament\Support\Icons\Heroicon;
use Tests\TestCase;

class SchoolModuleStandardConfigTest extends TestCase
{
    public function test_school_module_metadata_matches_standard_shape(): void
    {
        $config = require base_path('Modules/School/config/module.php');

        $this->assertSame('School', $config['name']);
        $this->assertSame('school', $config['alias']);
        $this->assertTrue($config['tenant_scoped']);
        $this->assertContains('Core', $config['depends_on']);
    }

    public function test_school_navigation_metadata_matches_current_resource_order(): void
    {
        $config = require base_path('Modules/School/config/navigation.php');

        $this->assertSame('School', $config['group']);
        $this->assertSame(Heroicon::AcademicCap, $config['icon']);
        $this->assertSame(50, $config['resources']['StudentResource']);
        $this->assertSame(200, $config['pages']['AcademicAnalytics']);
    }
}
