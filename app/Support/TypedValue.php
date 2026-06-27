<?php

namespace App\Support;

use Illuminate\Support\Arr;

/**
 * Safe narrowing helpers for PHPStan level 10 mixed values.
 */
final class TypedValue
{
    public static function int(mixed $value, int $default = 0): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        return $default;
    }

    public static function float(mixed $value, float $default = 0.0): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (float) $value;
        }

        return $default;
    }

    public static function string(mixed $value, string $default = ''): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return $default;
    }

    /**
     * @param  array<mixed>  $array
     */
    public static function intFromArray(array $array, int|string $key, int $default = 0): int
    {
        return self::int(Arr::get($array, $key), $default);
    }

    /**
     * @param  array<mixed>  $array
     */
    public static function stringFromArray(array $array, int|string $key, string $default = ''): string
    {
        return self::string(Arr::get($array, $key), $default);
    }

    /**
     * @param  array<mixed>  $array
     */
    public static function floatFromArray(array $array, int|string $key, float $default = 0.0): float
    {
        return self::float(Arr::get($array, $key), $default);
    }

    public static function tenantKey(mixed $key): int|string|null
    {
        return is_int($key) || is_string($key) ? $key : null;
    }

    public static function nullableInt(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    /**
     * @param  list<int>  $default
     * @return list<int>
     */
    public static function intList(mixed $value, array $default = [1, 2, 5, 10, 20, 30, 60]): array
    {
        if (! is_array($value)) {
            return $default;
        }

        $items = [];

        foreach ($value as $item) {
            if (is_int($item)) {
                $items[] = $item;

                continue;
            }

            if (is_string($item) && is_numeric($item)) {
                $items[] = (int) $item;
            }
        }

        /** @var list<int> $items */
        return $items;
    }
}
