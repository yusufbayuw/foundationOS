<?php

namespace App\Filament\Parent\Support\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\ParentStudent;

trait ScopesToParentChildren
{
    public static function getEloquentQuery(): Builder
    {
        $studentIds = ParentStudent::query()
            ->where('parent_user_id', auth()->id())
            ->pluck('student_id');

        return parent::getEloquentQuery()->whereIn('student_id', $studentIds);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
