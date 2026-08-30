<?php

namespace Tests\Feature;

use Modules\Core\Filament\Support\ModuleResource;
use Modules\Exam\Models\ExamActivityLog;
use Modules\Exam\Models\ExamAnswer;
use Modules\Exam\Models\ExamAttempt;
use Modules\Exam\Models\ExamAttemptSync;
use Modules\Exam\Models\ExamDefinitionQuestion;
use Modules\Exam\Models\ExamExportLog;
use Modules\Exam\Models\ExamGradebookExportLog;
use Modules\Exam\Models\ExamManualScore;
use Modules\Exam\Models\ExamPackage;
use Modules\Exam\Models\ExamPublishSnapshot;
use Modules\Exam\Models\ExamQuestionOption;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Models\ExamRuntimeSyncLog;
use Modules\Member\Models\MemberProfile;
use Modules\Member\Models\MemberProof;
use Modules\Monitoring\Models\AutomationRun;
use Modules\Monitoring\Models\PrintExportLog;
use Modules\Sales\Models\Voucher as SalesVoucher;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Tests\TestCase;

class ModuleFilamentResourceCoverageTest extends TestCase
{
    public function test_all_module_models_have_matching_filament_resources(): void
    {
        $resourceModels = collect($this->moduleResourceModels())->flip();

        $missingResources = collect($this->moduleModelClasses())
            ->reject(fn (string $modelClass): bool => $resourceModels->has($modelClass))
            ->values();

        $this->assertSame(
            [],
            $missingResources->all(),
            "Every module model should have a matching Filament resource.\nMissing resources:\n".$missingResources->implode("\n"),
        );
    }

    public function test_module_filament_resources_are_loadable_module_resources(): void
    {
        $resourceClasses = collect($this->moduleResourceClasses())
            ->filter(fn (string $resourceClass): bool => class_exists($resourceClass))
            ->filter(fn (string $resourceClass): bool => is_subclass_of($resourceClass, ModuleResource::class))
            ->reject(fn (string $resourceClass): bool => (new ReflectionClass($resourceClass))->isAbstract())
            ->values();

        $this->assertNotEmpty($resourceClasses);

        $resourceClasses->each(function (string $resourceClass): void {
            $this->assertTrue(
                is_subclass_of($resourceClass, ModuleResource::class),
                "{$resourceClass} should extend ".ModuleResource::class,
            );

            $modelClass = $resourceClass::getModel();

            $this->assertTrue(
                is_string($modelClass) && class_exists($modelClass),
                "{$resourceClass} should resolve an existing model class.",
            );
        });
    }

    /**
     * @return array<int, class-string>
     */
    private function moduleModelClasses(): array
    {
        $modulesPath = base_path('Modules');

        if (! is_dir($modulesPath)) {
            return [];
        }

        $classes = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($modulesPath));

        foreach ($files as $file) {
            if (! $file instanceof SplFileInfo || ! $file->isFile()) {
                continue;
            }

            if ($file->getExtension() !== 'php') {
                continue;
            }

            if (! str_contains($file->getPathname(), DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR)) {
                continue;
            }

            $class = $this->classNameFromFile($file->getPathname());

            if ($class === null || ! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            if ($reflection->isAbstract()) {
                continue;
            }

            if ($this->isExcludedFromFilamentResourceCoverage($class)) {
                continue;
            }

            $classes[] = $class;
        }

        sort($classes);

        return $classes;
    }

    /**
     * Models without top-level Filament resources (relation managers, pipelines, or audit logs).
     *
     * @return list<class-string>
     */
    private function modelsExcludedFromFilamentResourceCoverage(): array
    {
        return [
            PrintExportLog::class,
            AutomationRun::class,
            ExamActivityLog::class,
            ExamAnswer::class,
            ExamAttempt::class,
            ExamAttemptSync::class,
            ExamDefinitionQuestion::class,
            ExamExportLog::class,
            ExamGradebookExportLog::class,
            ExamManualScore::class,
            ExamPackage::class,
            ExamPublishSnapshot::class,
            ExamQuestionOption::class,
            ExamResult::class,
            ExamRuntimeSyncLog::class,
            MemberProfile::class,
            MemberProof::class,
            SalesVoucher::class,
        ];
    }

    private function isExcludedFromFilamentResourceCoverage(string $modelClass): bool
    {
        return in_array($modelClass, $this->modelsExcludedFromFilamentResourceCoverage(), true);
    }

    /**
     * @return array<int, class-string>
     */
    private function moduleResourceModels(): array
    {
        return collect($this->moduleResourceClasses())
            ->filter(fn (string $resourceClass): bool => class_exists($resourceClass))
            ->filter(fn (string $resourceClass): bool => is_subclass_of($resourceClass, ModuleResource::class))
            ->reject(fn (string $resourceClass): bool => (new ReflectionClass($resourceClass))->isAbstract())
            ->map(fn (string $resourceClass): string => $resourceClass::getModel())
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, class-string>
     */
    private function moduleResourceClasses(): array
    {
        $modulesPath = base_path('Modules');

        if (! is_dir($modulesPath)) {
            return [];
        }

        $classes = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($modulesPath));

        foreach ($files as $file) {
            if (! $file instanceof SplFileInfo || ! $file->isFile()) {
                continue;
            }

            if (! str_ends_with($file->getBasename(), 'Resource.php')) {
                continue;
            }

            if (! str_contains($file->getPathname(), DIRECTORY_SEPARATOR.'Filament'.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR)) {
                continue;
            }

            $class = $this->classNameFromFile($file->getPathname());

            if ($class !== null) {
                $classes[] = $class;
            }
        }

        sort($classes);

        return $classes;
    }

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
