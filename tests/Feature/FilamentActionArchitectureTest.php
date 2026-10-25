<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class FilamentActionArchitectureTest extends TestCase
{
    /**
     * One PHPUnit case checks every discovered Filament source file.
     *
     * Previously two data providers expanded this into 6,744 PHPUnit cases;
     * the contract is unchanged, but violations are now aggregated by file.
     */
    public function test_filament_sources_respect_io_boundaries(): void
    {
        $violations = [];
        $filesChecked = 0;

        foreach (self::filamentSourceFiles() as $file) {
            $path = $file['path'];
            $source = $this->readSource($path);
            $filesChecked++;

            if (preg_match('/\bHttp::/', $source)) {
                $violations[] = "Filament UI code must not call Http:: directly: {$path}";
            }

            if (! $this->containsBulkAction($source) || ! $this->containsActionClosure($source)) {
                continue;
            }

            $hasLoop = (bool) preg_match('/\bforeach\s*\(|->each\s*\(/', $source);
            $hasExternalIo = (bool) preg_match('/\b(?:Http|Mail)::|Notification::send\s*\(|(?:Import|Export)Action::make\s*\(|Storage::(?:put|copy|move|delete|readStream|writeStream)\s*\(/', $source);
            $dispatchesJob = (bool) preg_match('/(?:\bdispatch\s*\(|::dispatch\s*\(|Bus::(?:dispatch|batch|chain)\s*\()/', $source);

            if ($hasLoop && $hasExternalIo && ! $dispatchesJob) {
                $violations[] = "Bulk Filament action loops over external IO without dispatching a job: {$path}";
            }
        }

        $this->assertGreaterThan(0, $filesChecked, 'No Filament source files were discovered.');
        $this->assertSame([], $violations, implode("\n", $violations));
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
