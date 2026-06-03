<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhpStanBaselinePolicyTest extends TestCase
{
    #[Test]
    public function baseline_does_not_ignore_app_layer(): void
    {
        $baseline = file_get_contents(base_path('phpstan-baseline.neon'));

        $this->assertIsString($baseline);
        $this->assertDoesNotMatchRegularExpression('/path: app\//', $baseline);
    }

    #[Test]
    public function baseline_entry_count_stays_within_gelombang5_budget(): void
    {
        $baseline = file_get_contents(base_path('phpstan-baseline.neon'));

        $this->assertIsString($baseline);
        $this->assertLessThanOrEqual(
            220,
            substr_count($baseline, 'identifier:'),
            'Regenerate baseline after fixes; target is gradual reduction below 220 entries.',
        );
    }
}
