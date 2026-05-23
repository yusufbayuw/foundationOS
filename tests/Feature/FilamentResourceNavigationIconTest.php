<?php

namespace Tests\Feature;

use BackedEnum;
use Filament\Resources\Resource;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Tests\TestCase;

class FilamentResourceNavigationIconTest extends TestCase
{
    public function test_all_filament_resources_have_navigation_icons(): void
    {
        $resourceClasses = collect([
            app_path('Filament'),
            base_path('Modules'),
        ])
            ->flatMap(fn (string $path): array => $this->resourceClassesIn($path))
            ->filter(fn (string $class): bool => class_exists($class))
            ->filter(fn (string $class): bool => is_subclass_of($class, Resource::class))
            ->reject(fn (string $class): bool => (new ReflectionClass($class))->isAbstract())
            ->values();

        $this->assertNotEmpty($resourceClasses);

        $icons = $resourceClasses
            ->mapWithKeys(function (string $class): array {
                $icon = $class::getNavigationIcon();

                $this->assertNotNull($icon, "{$class} is missing a navigation icon.");
                $this->assertTrue(
                    is_string($icon) || $icon instanceof BackedEnum,
                    "{$class} navigation icon must be a string or backed enum.",
                );

                return [$class => $icon instanceof BackedEnum ? $icon::class.'::'.$icon->name : $icon];
            });

        $this->assertGreaterThanOrEqual(30, $icons->unique()->count());
    }

    /**
     * @return array<int, class-string>
     */
    private function resourceClassesIn(string $path): array
    {
        if (! is_dir($path)) {
            return [];
        }

        $classes = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        foreach ($files as $file) {
            if (! $file instanceof SplFileInfo || ! $file->isFile()) {
                continue;
            }

            if ($file->getBasename() !== 'Resource.php' && ! str_ends_with($file->getBasename(), 'Resource.php')) {
                continue;
            }

            if (str_contains($file->getPathname(), DIRECTORY_SEPARATOR.'Pages'.DIRECTORY_SEPARATOR)) {
                continue;
            }

            $class = $this->classNameFromFile($file->getPathname());

            if ($class !== null) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

    /**
     * @return class-string|null
     */
    private function classNameFromFile(string $path): ?string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        preg_match('/^namespace\s+([^;]+);/m', $contents, $namespace);
        preg_match('/^(?:abstract\s+)?class\s+([A-Za-z0-9_]+)/m', $contents, $class);

        if (! isset($namespace[1], $class[1])) {
            return null;
        }

        return $namespace[1].'\\'.$class[1];
    }
}
