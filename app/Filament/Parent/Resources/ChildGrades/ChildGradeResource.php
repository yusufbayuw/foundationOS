<?php

namespace App\Filament\Parent\Resources\ChildGrades;

use App\Filament\Parent\Resources\ChildGrades\Pages\ListChildGrades;
use App\Filament\Parent\Resources\ChildGrades\Tables\ChildGradesTable;
use App\Filament\Parent\Support\Concerns\ScopesToParentChildren;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\School\Models\StudentGrade;

class ChildGradeResource extends Resource
{
    use ScopesToParentChildren;

    protected static ?string $model = StudentGrade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $slug = 'child-grades';

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Grades');
    }

    public static function table(Table $table): Table
    {
        return ChildGradesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChildGrades::route('/'),
        ];
    }
}
