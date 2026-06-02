<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModuleMaturityConfigTest extends TestCase
{
    #[DataProvider('gaModulesProvider')]
    public function test_ga_modules_are_not_listed_as_experimental(string $module): void
    {
        $config = config('fos_module_maturity');

        $this->assertContains($module, $config['ga']);
        $this->assertNotContains($module, $config['experimental']);
    }

    public function test_definition_of_done_lists_required_criteria(): void
    {
        $criteria = config('fos_module_maturity.definition_of_done');

        $this->assertNotEmpty($criteria);
        $this->assertContains('BelongsToTenant on all operational models', $criteria);
        $this->assertContains('Factory + feature tests for primary flows', $criteria);
    }

    public function test_maturing_modules_are_not_listed_as_experimental(): void
    {
        $config = config('fos_module_maturity');

        foreach (['Risk', 'Donation', 'Sales'] as $module) {
            $this->assertContains($module, $config['maturing']);
            $this->assertNotContains($module, $config['experimental']);
        }
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function gaModulesProvider(): array
    {
        return [
            'core' => ['Core'],
            'school' => ['School'],
            'workflow' => ['Workflow'],
        ];
    }
}
