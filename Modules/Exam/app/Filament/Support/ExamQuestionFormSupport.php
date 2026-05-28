<?php

namespace Modules\Exam\Filament\Support;

use Filament\Forms\Components\TextInput;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Models\ExamQuestion;

class ExamQuestionFormSupport
{
    /** @var list<string> */
    public const MI_KEYS = [
        'logical',
        'linguistic',
        'visual',
        'kinesthetic',
        'interpersonal',
        'intrapersonal',
        'musical',
        'naturalist',
    ];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function dehydrateFormData(array $data): array
    {
        $miMapping = [];

        foreach (self::MI_KEYS as $key) {
            $field = 'mi_'.$key;

            if (array_key_exists($field, $data) && $data[$field] !== null && $data[$field] !== '') {
                $miMapping[$key] = (float) $data[$field];
            }

            unset($data[$field]);
        }

        if ($miMapping !== []) {
            $data['mi_mapping_json'] = $miMapping;
        }

        $metadata = is_array($data['metadata_json'] ?? null) ? $data['metadata_json'] : [];

        foreach (['olympiad_subject', 'olympiad_level', 'skill_codes', 'estimated_time_seconds'] as $key) {
            if (array_key_exists($key, $data)) {
                if ($data[$key] !== null && $data[$key] !== '' && $data[$key] !== []) {
                    $metadata[$key] = $data[$key];
                }

                unset($data[$key]);
            }
        }

        $data['metadata_json'] = $metadata !== [] ? $metadata : null;

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public static function hydrateFormData(ExamQuestion $record): array
    {
        $data = $record->attributesToArray();

        foreach (self::MI_KEYS as $key) {
            $data['mi_'.$key] = $record->mi_mapping_json[$key] ?? null;
        }

        $metadata = $record->metadata_json ?? [];
        $data['olympiad_subject'] = $metadata['olympiad_subject'] ?? null;
        $data['olympiad_level'] = $metadata['olympiad_level'] ?? null;
        $data['skill_codes'] = $metadata['skill_codes'] ?? [];
        $data['estimated_time_seconds'] = $metadata['estimated_time_seconds'] ?? null;

        return $data;
    }

    public static function miFieldsSchema(): array
    {
        $fields = [];

        foreach (self::MI_KEYS as $key) {
            $fields[] = TextInput::make('mi_'.$key)
                ->label(FilamentUi::text(ucfirst($key).' intelligence'))
                ->numeric()
                ->minValue(0)
                ->maxValue(1)
                ->step(0.01);
        }

        return $fields;
    }
}
