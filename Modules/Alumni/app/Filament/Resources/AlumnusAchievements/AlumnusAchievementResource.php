<?php

namespace Modules\Alumni\Filament\Resources\AlumnusAchievements;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\Pages\CreateAlumnusAchievement;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\Pages\EditAlumnusAchievement;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\Pages\ListAlumnusAchievements;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\Pages\ViewAlumnusAchievement;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\Schemas\AlumnusAchievementForm;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\Schemas\AlumnusAchievementInfolist;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\Tables\AlumnusAchievementsTable;
use Modules\Alumni\Models\AlumnusAchievement;
use Modules\Core\Filament\Support\ModuleResource;

class AlumnusAchievementResource extends ModuleResource
{
    protected static ?string $model = AlumnusAchievement::class;

    public static function form(Schema $schema): Schema
    {
        return AlumnusAchievementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AlumnusAchievementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlumnusAchievementsTable::configure($table);
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
            'index' => ListAlumnusAchievements::route('/'),
            'create' => CreateAlumnusAchievement::route('/create'),
            'view' => ViewAlumnusAchievement::route('/{record}'),
            'edit' => EditAlumnusAchievement::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
