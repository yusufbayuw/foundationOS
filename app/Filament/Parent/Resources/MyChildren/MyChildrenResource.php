<?php

namespace App\Filament\Parent\Resources\MyChildren;

use App\Filament\Parent\Resources\MyChildren\Pages\ListMyChildren;
use App\Filament\Parent\Resources\MyChildren\Tables\MyChildrenTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\ParentStudent;
use Modules\Core\Support\FilamentUi;
use Modules\School\Models\Student;

class MyChildrenResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $slug = 'my-children';

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('My children');
    }

    public static function getModelLabel(): string
    {
        return FilamentUi::text('Child');
    }

    public static function getPluralModelLabel(): string
    {
        return FilamentUi::text('My children');
    }

    public static function getEloquentQuery(): Builder
    {
        $parentId = auth()->id();

        return parent::getEloquentQuery()
            ->whereIn('id', ParentStudent::query()
                ->where('parent_user_id', $parentId)
                ->pluck('student_id'));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return MyChildrenTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyChildren::route('/'),
        ];
    }
}
