<?php

namespace Modules\School\Filament\Resources\StudentAchievements;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\School\Filament\Resources\StudentAchievements\Pages\CreateStudentAchievement;
use Modules\School\Filament\Resources\StudentAchievements\Pages\EditStudentAchievement;
use Modules\School\Filament\Resources\StudentAchievements\Pages\ListStudentAchievements;
use Modules\School\Filament\Resources\StudentAchievements\Pages\ViewStudentAchievement;
use Modules\School\Filament\Resources\StudentAchievements\Schemas\StudentAchievementForm;
use Modules\School\Filament\Resources\StudentAchievements\Schemas\StudentAchievementInfolist;
use Modules\School\Filament\Resources\StudentAchievements\Tables\StudentAchievementsTable;
use Modules\School\Models\StudentAchievement;

class StudentAchievementResource extends LocalizedResource
{
    protected static ?string $model = StudentAchievement::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudentAchievementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentAchievementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentAchievementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentAchievements::route('/'),
            'create' => CreateStudentAchievement::route('/create'),
            'view' => ViewStudentAchievement::route('/{record}'),
            'edit' => EditStudentAchievement::route('/{record}/edit'),
        ];
    }
}
