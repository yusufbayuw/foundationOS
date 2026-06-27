<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\Concerns\BelongsToTenant;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreatesTenantForTests;
use Tests\Support\ThinGaModuleCatalog;
use Tests\TestCase;

/**
 * H2 — structural thin-module coverage: PHPUnit includes Modules/, factories, BelongsToTenant, feature tests.
 */
class ThinGaModuleTestH2Test extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_phpunit_source_includes_modules_directory(): void
    {
        $xml = file_get_contents(base_path('phpunit.xml'));

        $this->assertIsString($xml);
        $this->assertStringContainsString('Modules/', $xml);
        $this->assertStringContainsString('<directory', $xml);
        $this->assertStringContainsString('app</directory>', $xml);
    }

    #[DataProvider('thinGaModuleProvider')]
    public function test_thin_ga_module_has_factory_trait_and_tenant_scope(
        string $key,
        array $modules,
        string $modelClass,
        string $featureTestFile,
    ): void {
        unset($modules);
        $this->assertTrue(class_exists($modelClass), "Model for [{$key}] should exist.");
        $this->assertContains(
            BelongsToTenant::class,
            class_uses_recursive($modelClass),
            "{$modelClass} should use BelongsToTenant.",
        );

        $model = new $modelClass;
        $this->assertTrue(
            method_exists($model, 'factory'),
            "{$modelClass} should expose a model factory.",
        );

        $factoryPath = base_path('Modules/'.ucfirst($key).'/database/factories');
        if (! is_dir($factoryPath)) {
            $factoryPath = base_path('Modules/'.$this->moduleFolderName($key).'/database/factories');
        }

        $this->assertDirectoryExists($factoryPath, "Factory directory for [{$key}] should exist.");
        $this->assertNotEmpty(glob($factoryPath.'/*Factory.php'));

        $featureTestPath = base_path('tests/Feature/'.$featureTestFile);
        $this->assertFileExists(
            $featureTestPath,
            "Thin module [{$key}] should have a dedicated feature test ({$featureTestFile}).",
        );
    }

    #[DataProvider('thinGaModuleFactoryProvider')]
    public function test_thin_ga_module_factory_persists_for_tenant(
        array $modules,
        string $modelClass,
    ): void {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext($modules);

        $record = $modelClass::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertSame($tenant->id, $record->tenant_id);
        $this->assertNotEmpty($record->getKey());
    }

    /**
     * @return array<string, array{0: string, 1: list<string>|string, 2?: string, 3?: string}>
     */
    public static function thinGaModuleProvider(): array
    {
        $cases = [];

        foreach (ThinGaModuleCatalog::entries() as $entry) {
            $cases[$entry['key']] = [
                $entry['key'],
                $entry['modules'],
                $entry['model'],
                $entry['feature_test'],
            ];
        }

        return $cases;
    }

    /**
     * @return array<string, array{0: list<string>, 1: class-string}>
     */
    public static function thinGaModuleFactoryProvider(): array
    {
        $cases = [];

        foreach (ThinGaModuleCatalog::entries() as $entry) {
            $cases[$entry['key']] = [
                $entry['modules'],
                $entry['model'],
            ];
        }

        return $cases;
    }

    private function moduleFolderName(string $key): string
    {
        return match ($key) {
            'physicalsecurity' => 'PhysicalSecurity',
            'merchorder' => 'MerchOrder',
            default => ucfirst($key),
        };
    }
}
