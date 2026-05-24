<?php

namespace Modules\Core\Filament\Support\Labels;

use Modules\Core\Support\FilamentUi;

class ResourceLabelResolver
{
    public static function navigationGroup(string $module): string
    {
        return FilamentUi::module($module);
    }

    public static function navigationLabel(string $pluralModelLabel): string
    {
        return self::toProperCase(FilamentUi::text($pluralModelLabel));
    }

    public static function modelLabel(string $modelClass): string
    {
        return FilamentUi::resource($modelClass);
    }

    public static function pluralModelLabel(string $pluralModelLabel): string
    {
        return FilamentUi::text($pluralModelLabel);
    }

    public static function toProperCase(string $value): string
    {
        return collect(preg_split('/(\s+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [])
            ->map(function (string $token): string {
                if (trim($token) === '') {
                    return $token;
                }

                $parts = preg_split('/([\\-\\/])/', $token, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$token];

                $parts = array_map(function (string $part): string {
                    if ($part === '-' || $part === '/') {
                        return $part;
                    }

                    if (preg_match('/^[A-Z0-9]+$/', $part)) {
                        return $part;
                    }

                    $lower = strtolower($part);

                    return ucfirst($lower);
                }, $parts);

                return implode('', $parts);
            })
            ->implode('');
    }
}
