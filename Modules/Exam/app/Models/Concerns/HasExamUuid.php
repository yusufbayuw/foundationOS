<?php

namespace Modules\Exam\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasExamUuid
{
    public static function bootHasExamUuid(): void
    {
        static::creating(function (Model $model): void {
            $key = $model->getKeyName();

            if (empty($model->{$key})) {
                $model->{$key} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
