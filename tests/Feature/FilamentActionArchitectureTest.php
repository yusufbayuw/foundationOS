<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FilamentActionArchitectureTest extends TestCase
{
    #[DataProvider('filamentSourceFiles')]
    public function test_filament_sources_do_not_call_http_client_directly(string $path): void
    {
        $source = $this->readSource($path);

        $this->assertDoesNotMatchRegularExpression(
            '/\\bHttp::/',
            $source,
            "Filament UI code must not call Http:: directly. Move external IO to an injected service or queued job: {$path}",
        );
    }

    #[DataProvider('filamentSourceFiles')]
    public function test_bulk_actions_do_not_loop_over_external_io_without_dispatching_jobs(string $path): void
    {
        $source = $this->readSource($path);

        if (! $this->containsBulkAction($source) || ! $this->containsActionClosure($source)) {
            $this->addToAssertionCount(1);

            return;
        }

        $hasLoop = (bool) preg_match('/\\bforeach\\s*\\(|->each\\s*\\(/', $source);
        $hasExternalIo = (bool) preg_match('/\\b(?:Http|Mail)::|Notification::send\\s*\\(|(?:Import|Export)Action::make\\s*\\(|Storage::(?:put|copy|move|delete|readStream|writeStream)\\s*\\(/', $source);
        $dispatchesJob = (bool) preg_match('/(?:\\bdispatch\\s*\\(|::dispatch\\s*\\(|Bus::(?:dispatch|batch|chain)\\s*\\()/', $source);

        $this->assertFalse(
            $hasLoop && $hasExternalIo && ! $dispatchesJob,
            "Bulk Filament actions that loop over external IO must dispatch a job instead of processing synchronously: {$path}",
        );
    }

    /**
     * @return iterable<string, array{path: string}>
     */
    public static function filamentSourceFiles(): iterable
    {
        $root = dirname(__DIR__, 2);
        $directories = [
            $root.'/app/Filament',
            ...glob($root.'/Modules/*/app/Filament', GLOB_ONLYDIR),
        ];

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            );

            foreach ($iterator as $file) {
                if (! $file instanceof \SplFileInfo || $file->getExtension() !== 'php') {
                    continue;
                }

                $path = $file->getPathname();
                $relativePath = str_replace($root.'/', '', $path);

                yield $relativePath => ['path' => $path];
            }
        }
    }

    private function readSource(string $path): string
    {
        $source = file_get_contents($path);

        $this->assertIsString($source, "Unable to read Filament source file: {$path}");

        return $source;
    }

    private function containsBulkAction(string $source): bool
    {
        return str_contains($source, 'BulkAction::make')
            || str_contains($source, 'BulkActionGroup::make');
    }

    private function containsActionClosure(string $source): bool
    {
        return (bool) preg_match('/->action\\s*\\(\\s*(?:static\\s+)?function\\b/', $source);
    }
}
