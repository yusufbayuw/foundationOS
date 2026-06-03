<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\BootstrapsFilamentAdmin;
use Tests\Concerns\CreatesTenantForTests;
use Tests\Support\ThinGaModuleCatalog;
use Tests\TestCase;

/**
 * H3 — thin GA modules: Filament list pages render under tenant context (Livewire smoke).
 */
class ThinGaModuleTestH3Test extends TestCase
{
    use BootstrapsFilamentAdmin;
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        $this->tearDownFilamentAdmin();

        parent::tearDown();
    }

    #[DataProvider('thinGaModuleListPageProvider')]
    public function test_thin_ga_module_list_page_renders(
        string $key,
        array $modules,
        string $listPageClass,
    ): void {
        $this->bootstrapFilamentAdmin($modules);

        Livewire::test($listPageClass)
            ->assertSuccessful();
    }

    /**
     * @return array<string, array{0: string, 1: list<string>, 2: class-string}>
     */
    public static function thinGaModuleListPageProvider(): array
    {
        $cases = [];

        foreach (ThinGaModuleCatalog::entries() as $entry) {
            $cases[$entry['key']] = [
                $entry['key'],
                $entry['modules'],
                $entry['list_page'],
            ];
        }

        return $cases;
    }
}
