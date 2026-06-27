<?php

namespace Tests\Regression;

use Modules\Workflow\Support\JsonLogicEvaluator;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Regression: deeply nested JSON Logic must not recurse indefinitely.
 */
#[Group('regression')]
class JsonLogicDepthRegressionTest extends TestCase
{
    public function test_excessive_nesting_returns_false_instead_of_overflowing(): void
    {
        $logic = $this->nestedAndLogic(40);

        $result = JsonLogicEvaluator::apply($logic, ['approved' => true]);

        $this->assertFalse($result);
    }

    public function test_reasonable_nesting_still_evaluates(): void
    {
        $logic = [
            'and' => [
                ['==' => [['var' => 'status'], 'pending']],
                ['>' => [['var' => 'amount'], 1000]],
            ],
        ];

        $this->assertTrue(JsonLogicEvaluator::apply($logic, ['status' => 'pending', 'amount' => 5000]));
        $this->assertFalse(JsonLogicEvaluator::apply($logic, ['status' => 'pending', 'amount' => 100]));
    }

    /**
     * @return array<string, mixed>
     */
    private function nestedAndLogic(int $depth): array
    {
        if ($depth <= 0) {
            return ['var' => 'approved'];
        }

        return ['and' => [$this->nestedAndLogic($depth - 1)]];
    }
}
