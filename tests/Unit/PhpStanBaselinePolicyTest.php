<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhpStanBaselinePolicyTest extends TestCase
{
    #[Test]
    public function phpstan_config_targets_maximum_level_without_baseline(): void
    {
        $config = file_get_contents(base_path('phpstan.neon'));

        $this->assertIsString($config);
        $this->assertStringContainsString('level: 10', $config);
        $this->assertStringNotContainsString('phpstan-baseline.neon', $config);
        $this->assertFileDoesNotExist(base_path('phpstan-baseline.neon'));
    }
}
