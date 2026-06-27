<?php

namespace Modules\Workflow\Contracts;

interface RuleEngine
{
    /**
     * @param  array<string, mixed>  $rule
     * @param  array<string, mixed>  $context
     */
    public function matches(array $rule, array $context): bool;

    /**
     * @param  list<array<string, mixed>>  $schema
     * @param  array<string, mixed>  $context
     * @return list<array<string, mixed>>
     */
    public function evaluateFieldState(array $schema, array $context): array;

    /**
     * @param  array<int, array<string, mixed>>  $rules
     * @param  array<string, mixed>  $context
     */
    public function resolveCandidates(iterable $rules, array $context): mixed;
}
