<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class WorkflowViteBundleSeparationTest extends TestCase
{
    public function test_workflow_designer_is_separate_vite_entry_from_app_bundle(): void
    {
        $viteConfig = file_get_contents(base_path('vite.config.js'));
        $appJs = file_get_contents(base_path('resources/js/app.js'));

        $this->assertIsString($viteConfig);
        $this->assertStringContainsString('resources/js/workflow-designer.js', $viteConfig);
        $this->assertStringNotContainsString('workflow-designer', $appJs);
        $this->assertStringNotContainsString('cytoscape', $appJs);
    }

    public function test_workflow_canvas_blade_loads_designer_entry_via_vite_directive(): void
    {
        $blade = file_get_contents(base_path('Modules/Workflow/resources/views/livewire/workflow-canvas.blade.php'));

        $this->assertIsString($blade);
        $this->assertStringContainsString("@vite('resources/js/workflow-designer.js')", $blade);
    }
}
