<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageReflectsCurrentFeaturesTest extends TestCase
{
    public function test_landing_page_highlights_current_product_surfaces(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Integrated Education Foundation OS')
            ->assertSee('Admin Panel')
            ->assertSee('/admin', false)
            ->assertSee('Platform Console')
            ->assertSee('/platform', false)
            ->assertSee('Parent Portal')
            ->assertSee('/parent', false)
            ->assertSee('Workflow Designer')
            ->assertSee('Moodle reconciliation')
            ->assertSee('Risk, audit, ISO, and education QA');
    }
}
