<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ArtisanBootstrapMemoryTest extends TestCase
{
    public function test_artisan_bootstraps_when_php_starts_with_128_megabytes(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $process = new Process([
            PHP_BINARY,
            '-d',
            'memory_limit=128M',
            $projectRoot.'/artisan',
            '--version',
        ], $projectRoot, [
            'FOUNDATIONOS_CLI_MEMORY_LIMIT' => '64M',
        ]);

        $process->setTimeout(60);
        $process->run();

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        $this->assertStringContainsString('Laravel Framework', $process->getOutput());
    }
}
