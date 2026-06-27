<?php

namespace Modules\Core\Filament\Concerns;

use Modules\Core\Services\ContextDefaults;

/**
 * Merges tenant/org/academic context into Filament create form data.
 */
trait AppliesContextDefaults
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $context = app(ContextDefaults::class)->forCreate();

        foreach ($context as $key => $value) {
            if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
                $data[$key] = $value;
            }
        }

        return parent::mutateFormDataBeforeCreate($data);
    }
}
