<?php

namespace Modules\Workflow\Support;

class JsonLogicEvaluator
{
    private const MAX_DEPTH = 32;

    public static function apply(mixed $logic, array $data = [], int $depth = 0): mixed
    {
        if ($depth > self::MAX_DEPTH) {
            return false;
        }

        if ($logic === null || is_scalar($logic)) {
            return $logic;
        }

        if (is_array($logic) && array_is_list($logic)) {
            return array_map(fn ($item) => static::apply($item, $data, $depth + 1), $logic);
        }

        if (! is_array($logic)) {
            return $logic;
        }

        if (count($logic) !== 1) {
            return $logic;
        }

        $operator = array_key_first($logic);
        $values = $logic[$operator];
        $values = is_array($values) ? $values : [$values];

        return match ($operator) {
            'var' => static::resolveVar($values, $data),
            'and' => static::applyAnd($values, $data, $depth + 1),
            'or' => static::applyOr($values, $data, $depth + 1),
            '!' => ! static::truthy(static::apply($values[0] ?? null, $data, $depth + 1)),
            '==' => static::apply($values[0] ?? null, $data, $depth + 1) == static::apply($values[1] ?? null, $data, $depth + 1),
            '===' => static::apply($values[0] ?? null, $data, $depth + 1) === static::apply($values[1] ?? null, $data, $depth + 1),
            '!=' => static::apply($values[0] ?? null, $data, $depth + 1) != static::apply($values[1] ?? null, $data, $depth + 1),
            '!==' => static::apply($values[0] ?? null, $data, $depth + 1) !== static::apply($values[1] ?? null, $data, $depth + 1),
            '>' => static::apply($values[0] ?? null, $data, $depth + 1) > static::apply($values[1] ?? null, $data, $depth + 1),
            '>=' => static::apply($values[0] ?? null, $data, $depth + 1) >= static::apply($values[1] ?? null, $data, $depth + 1),
            '<' => static::apply($values[0] ?? null, $data, $depth + 1) < static::apply($values[1] ?? null, $data, $depth + 1),
            '<=' => static::apply($values[0] ?? null, $data, $depth + 1) <= static::apply($values[1] ?? null, $data, $depth + 1),
            'in' => static::applyIn($values, $data, $depth + 1),
            '+' => array_sum(array_map(fn ($item) => (float) static::apply($item, $data, $depth + 1), $values)),
            default => false,
        };
    }

    protected static function resolveVar(array $values, array $data): mixed
    {
        $path = (string) ($values[0] ?? '');
        $default = $values[1] ?? null;

        if ($path === '') {
            return $data;
        }

        $segments = explode('.', $path);
        $current = $data;

        foreach ($segments as $segment) {
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];

                continue;
            }

            return $default;
        }

        return $current;
    }

    protected static function applyAnd(array $values, array $data, int $depth): bool
    {
        foreach ($values as $value) {
            if (! static::truthy(static::apply($value, $data, $depth))) {
                return false;
            }
        }

        return true;
    }

    protected static function applyOr(array $values, array $data, int $depth): bool
    {
        foreach ($values as $value) {
            if (static::truthy(static::apply($value, $data, $depth))) {
                return true;
            }
        }

        return false;
    }

    protected static function applyIn(array $values, array $data, int $depth): bool
    {
        $needle = static::apply($values[0] ?? null, $data, $depth);
        $haystack = static::apply($values[1] ?? [], $data, $depth);

        if (is_array($haystack)) {
            return in_array($needle, $haystack, true);
        }

        if (is_string($haystack)) {
            return str_contains($haystack, (string) $needle);
        }

        return false;
    }

    protected static function truthy(mixed $value): bool
    {
        if (is_array($value)) {
            return count($value) > 0;
        }

        return (bool) $value;
    }
}
