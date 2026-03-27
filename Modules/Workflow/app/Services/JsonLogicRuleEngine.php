<?php

namespace Modules\Workflow\Services;

use Illuminate\Support\Collection;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Support\JsonLogicEvaluator;

class JsonLogicRuleEngine implements RuleEngine
{
    public function matches(array $rule, array $context): bool
    {
        if ($rule === []) {
            return true;
        }

        $evaluator = config('workflow.json_logic_class', JsonLogicEvaluator::class);

        return (bool) $evaluator::apply($rule, $context);
    }

    public function evaluateFieldState(array $schema, array $context): array
    {
        return collect($schema)
            ->map(function (array $field) use ($context): array {
                $visible = $this->evaluateBooleanRule($field['visibility_rules'] ?? null, $context, true);
                $disabled = $this->evaluateBooleanRule($field['disabled_rules'] ?? null, $context, false);
                $required = $this->evaluateBooleanRule($field['required_rules'] ?? null, $context, (bool) ($field['required'] ?? false));

                if (! array_key_exists('default_value', $field) && array_key_exists('default', $field)) {
                    $field['default_value'] = $field['default'];
                }

                $field['_visible'] = $visible;
                $field['_disabled'] = $disabled;
                $field['_required'] = $required;

                return $field;
            })
            ->values()
            ->all();
    }

    public function resolveCandidates(iterable $rules, array $context): mixed
    {
        $collection = $rules instanceof Collection ? $rules : collect($rules);

        return $collection
            ->filter(function ($candidate) use ($context): bool {
                $rule = (array) data_get($candidate, 'condition_rules', []);

                return $this->matches($rule, $context);
            })
            ->values();
    }

    protected function evaluateBooleanRule(mixed $rule, array $context, bool $default): bool
    {
        if ($rule === null || $rule === []) {
            return $default;
        }

        if (! is_array($rule)) {
            return $default;
        }

        return $this->matches($rule, $context);
    }
}
