<?php

namespace Modules\Exam\Support;

use Illuminate\Database\Eloquent\Model;
use Modules\Exam\Models\ExamModel;

class ExamRuntimeEntityRef
{
    /**
     * @return array{id: ?string, foundation_id: ?string, reference_type: ?string, reference_id: ?int}
     */
    public static function forModel(?Model $model): array
    {
        if ($model === null) {
            return [
                'id' => null,
                'foundation_id' => null,
                'reference_type' => null,
                'reference_id' => null,
            ];
        }

        if ($model instanceof ExamModel) {
            return [
                'id' => (string) $model->getKey(),
                'foundation_id' => (string) $model->getKey(),
                'reference_type' => null,
                'reference_id' => null,
            ];
        }

        return [
            'id' => null,
            'foundation_id' => null,
            'reference_type' => $model::class,
            'reference_id' => (int) $model->getKey(),
        ];
    }

    /**
     * @return array{id: ?int, uuid: ?string, code: ?string, name: ?string}
     */
    public static function forTenant(?Model $tenant): array
    {
        if ($tenant === null) {
            return ['id' => null, 'uuid' => null, 'code' => null, 'name' => null];
        }

        return [
            'id' => (int) $tenant->getKey(),
            'uuid' => $tenant->uuid ?? null,
            'code' => $tenant->code ?? null,
            'name' => $tenant->name ?? null,
        ];
    }
}
