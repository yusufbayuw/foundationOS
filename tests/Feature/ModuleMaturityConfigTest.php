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

    public function test_promoted_modules_are_ga_not_experimental(): void
    {
        $config = config('fos_module_maturity');

        foreach ([
            'Risk', 'Donation', 'Sales', 'Transport', 'Property', 'Training',
            'Marketplace', 'Cms', 'Legal', 'Asset', 'Helpdesk', 'Facility', 'EOffice',
            'Dms', 'ItOps', 'Boarding', 'Cafeteria',
            'PhysicalSecurity', 'Counseling', 'Clinic', 'Event',
            'MerchOrder', 'Alumni', 'Messaging', 'Printing',
        ] as $module) {
            $this->assertContains($module, $config['ga']);
            $this->assertNotContains($module, $config['experimental']);
        }
    }

    public function test_maturing_tier_is_empty_until_next_promotion(): void
    {
        $this->assertSame([], config('fos_module_maturity.maturing'));
    }

    public function test_experimental_tier_is_empty_after_final_ga_promotion(): void
    {
        $this->assertSame([], config('fos_module_maturity.experimental'));
    }

    public function test_all_enabled_modules_are_listed_as_ga(): void
    {
        $enabled = array_keys(array_filter(
            json_decode((string) file_get_contents(base_path('modules_statuses.json')), true),
            fn (bool $active): bool => $active,
        ));

        $ga = config('fos_module_maturity.ga');

        foreach ($enabled as $module) {
            $this->assertContains(
                $module,
                $ga,
                "Enabled module [{$module}] must be in fos_module_maturity.ga",
            );
        }
    }

    public function test_all_promoted_modules_are_ga(): void
    {
        $config = config('fos_module_maturity');

        foreach ([
            'InternalAudit', 'IsoCompliance', 'EducationQa', 'Exam',
            'KpiEnterprise', 'Capacity', 'Ai', 'Consulting',
        ] as $module) {
            $this->assertContains($module, $config['ga']);
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
