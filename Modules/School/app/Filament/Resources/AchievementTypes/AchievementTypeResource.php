<?php

namespace Modules\School\Filament\Resources\AchievementTypes;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\AchievementTypes\Pages\CreateAchievementType;
use Modules\School\Filament\Resources\AchievementTypes\Pages\EditAchievementType;
use Modules\School\Filament\Resources\AchievementTypes\Pages\ListAchievementTypes;
use Modules\School\Filament\Resources\AchievementTypes\Pages\ViewAchievementType;
use Modules\School\Filament\Resources\AchievementTypes\RelationManagers\StudentAchievementsRelationManager;
use Modules\School\Filament\Resources\AchievementTypes\Schemas\AchievementTypeForm;
use Modules\School\Filament\Resources\AchievementTypes\Schemas\AchievementTypeInfolist;
use Modules\School\Filament\Resources\AchievementTypes\Tables\AchievementTypesTable;
use Modules\School\Models\AchievementType;

class AchievementTypeResource extends LocalizedResource
{
    protected static ?string $model = AchievementType::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AchievementTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AchievementTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AchievementTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudentAchievementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAchievementTypes::route('/'),
            'create' => CreateAchievementType::route('/create'),
            'view' => ViewAchievementType::route('/{record}'),
            'edit' => EditAchievementType::route('/{record}/edit'),
        ];
    }
}
