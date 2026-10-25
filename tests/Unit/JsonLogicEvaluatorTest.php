<?php

namespace Tests\Unit;

use Modules\Workflow\Support\JsonLogicEvaluator;
use Tests\TestCase;

class JsonLogicEvaluatorTest extends TestCase
{
    public function test_var_resolves_nested_path_with_default(): void
    {
        $logic = ['var' => ['missing.path', 'fallback']];

        $this->assertSame('fallback', JsonLogicEvaluator::apply($logic, []));
    }

    public function test_and_short_circuits_on_false_operand(): void
    {
        $logic = [
            'and' => [
                ['==' => [['var' => 'a'], 1]],
                ['==' => [['var' => 'b'], 2]],
            ],
        ];

        $this->assertFalse(JsonLogicEvaluator::apply($logic, ['a' => 1, 'b' => 0]));
        $this->assertTrue(JsonLogicEvaluator::apply($logic, ['a' => 1, 'b' => 2]));
    }

    public function test_in_operator_checks_membership(): void
    {
        $logic = ['in' => [['var' => 'role'], ['admin', 'manager']]];

        $this->assertTrue(JsonLogicEvaluator::apply($logic, ['role' => 'admin']));
        $this->assertFalse(JsonLogicEvaluator::apply($logic, ['role' => 'guest']));
    }
}
