<?php

namespace Modules\Workflow\Contracts;

interface RuleEngine
{
    public function matches(array $rule, array $context): bool;

    public function evaluateFieldState(array $schema, array $context): array;

    public function resolveCandidates(iterable $rules, array $context): mixed;
}
